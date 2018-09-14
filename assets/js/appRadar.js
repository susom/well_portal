(function() {
  angular.module("RadarChart", [])
    .directive("radar", radar)
    .directive("onReadFile", onReadFile)
    .controller("MainCtrl", ['$http','$scope','$attrs', 

  // https://qiita.com/yorkxin/items/c5899314d63214fb5409
  // controller function MainCtrl
  function ($http,$scope,$attrs) {
    var ctrl = this;
    init();
    // function init
    function init() {
      // initialize controller variables
      ctrl.exampleSelected = $attrs.csvfile;

      ctrl.getData = getData;
      ctrl.selectExample = selectExample;

      // initialize controller functions
      
      ctrl.selectExample(ctrl.exampleSelected);
      ctrl.config = {
        w: radar_w,
        h: radar_h,
        facet: false,
        levels: 10,
        levelScale: 0.85,
        labelScale: labelScale,
        facetPaddingScale: 2.1,
        showLevels: true,
        showLevelsLabels: true,
        showAxesLabels: true,
        showAxes: true,
        showLegend: true,
        showVertices: true,
        showPolygons: true
      };
    }

    // function getData
    function getData($fileContent) {
      ctrl.csv = $fileContent;
    }

    // function selectExample
    function selectExample(item) {
      var file = item + ".csv";
      $http.get(file).success(function(data) {
        ctrl.csv = data;
      });
    }
  }]);


  // directive function sunburst
  function radar() {
    return {
      restrict: "E",
      scope: {
        csv: "=",
        config: "="
      },
      link: radarDraw
    };
  }


  // directive function onReadFile
  function onReadFile($parse) {
    return {
      restrict: "A",
      scope: false,
      link: function(scope, element, attrs) {
        var fn = $parse(attrs.onReadFile);
        element.on("change", function(onChangeEvent) {
          var reader = new FileReader();
          reader.onload = function(onLoadEvent) {
            scope.$apply(function() {
              fn(scope, {
                $fileContent: onLoadEvent.target.result
              });
            });
          };
          reader.readAsText((onChangeEvent.srcElement || onChangeEvent.target).files[0]);
        });
      }
    };
  }
})();