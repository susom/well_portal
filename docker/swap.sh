# Recreate the prod well-portal container on a given image tag (~5s downtime).
# Auto-reverts to the previously running image if login.php isn't 200.
# Run from your laptop (needs IAP SSH access to som-well-for-life):
#   gcloud compute ssh wellforlife-vm1 --zone=us-west1-b --project=som-well-for-life --tunnel-through-iap \
#     -- 'NEW=well-portal:<tag> bash -s' < docker/swap.sh
# See MAINTENANCE.md for building and shipping a new image first.
set -e
NEW=${NEW:?set NEW=well-portal:<tag>}
D=/mnt/disks/ext-disk/well-portal-ssh
PREV=$(docker inspect -f '{{.Config.Image}}' well-portal)
echo "prev=$PREV new=$NEW"

start() {
  docker rm -f well-portal >/dev/null 2>&1 || true
  # The code lives in the named volume (a git checkout); the image only supplies PHP/nginx.
  # Log rotation keeps the container's stdout/stderr log from growing forever.
  docker run -d --name well-portal --restart unless-stopped -p 80:80 \
    --log-opt max-size=10m --log-opt max-file=5 \
    -v well-portal:/var/www/html -v $D:/root/.ssh:ro "$1" >/dev/null
  sleep 5
  [ "$(curl -s -o /dev/null -w '%{http_code}' -H 'X-Forwarded-Proto: https' localhost/login.php)" = 200 ]
}

if start $NEW; then
  echo "== swapped to $NEW"
else
  echo "!! $NEW failed health check, reverting to $PREV"
  start $PREV && echo "== reverted to $PREV"
  exit 1
fi
rm -f /var/lib/docker/volumes/well-portal/_data/supervisord.pid 2>/dev/null || sudo rm -f /var/lib/docker/volumes/well-portal/_data/supervisord.pid
docker inspect -f 'log: {{.HostConfig.LogConfig.Config}}' well-portal
docker ps -a --filter name=well-portal --format '{{.Names}}  {{.Image}}  {{.Status}}'
