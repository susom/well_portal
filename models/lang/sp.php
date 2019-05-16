<?php
	/*
		UserPie Langauge File.
		Language: English.
	*/
	
	/*
		%m1% - Dymamic markers which are replaced at run time by the relevant index.
	*/

	$lang = array();
	
	//Account
	$lang = array_merge($lang,array(
		//ERROR AND POPUP
		"ACCOUNT_SPECIFY_F_L_NAME" 				=> "Por favor entre su nombre y apellido",
		"ACCOUNT_SPECIFY_USERNAME" 				=> "Por favor entre su nombre de usuario",
		"ACCOUNT_SPECIFY_PASSWORD" 				=> "Por favor entre su contraseña",
		"ACCOUNT_SPECIFY_EMAIL"					=> "Por favor entre su correo electrónico",
        "ACCOUNT_INVALID_EMAIL"		 			=> "Correo electrónico erróneo",
		"ACCOUNT_EMAIL_MISMATCH"				=> "Correos electrónicos no son idénticos",
		"ACCOUNT_USER_OR_PASS_INVALID"			=> "Correo electrónico y/o contraseña no reconocidos",
		"ACCOUNT_PASS_MISMATCH"					=> "Contraseñas no son idénticas",
		"ACCOUNT_EMAIL_IN_USE_ACTIVE"			=> "Correo electrónico %m1% ya está en uso. Sí ha olvidado su contraseña, puede ingresar una nueva <a href='login.php'>Formulario para Entrar</a>",
		"ACCOUNT_NEW_ACTIVATION_SENT"			=> "¡Gracias por registrarse con la Iniciativa de WELL Bien Para Vida! Enviamos un link de activación a su correo electrónico. Por favor verifique su correo y siga las instrucciones para activar su cuenta. Sí no recibe el mensaje dentro de una hora, contáctenos a wellforlife@stanford.edu", 
		"ACCOUNT_SPECIFY_NEW_PASSWORD"			=> "Por favor entre su contraseña nueva",	
		"ACCOUNT_NOT_YET_ELIGIBLE"				=> "¡Gracias por su interés en WELL Bien Para Vida! No es elegible para participar en este momento. %m1% Estaremos en comunicación con usted sobre estudios relacionados a WELL Bien Para Vida y con más información al expandir nuestro proyecto.",
		"ACCOUNT_NEED_LOCATION"					=> "Por favor entre su código postal o ciudad",
		"ACCOUNT_TOO_YOUNG"						=> "Todavía no tiene 18 años.", 
		"ACCOUNT_NOT_IN_USA"					=> "Este estudio es sólo para participantes viviendo en los Estados Unidos.", 
		"ACTIVATION_MESSAGE"					=> "Tiene que activar su cuenta para poder entrar. Siga la siguiente página para activar su cuenta. \n\n%m1%register.php?activation=%m2%",
		"ACCOUNT_ERROR_TRY_AGAIN"				=> "Intente  otra vez…. Le queda ", 
		"ACCOUNT_ERROR_ATTEMPTS"				=> " oportunidades.",
		"ACCOUNT_ERROR_ATTEMPT"					=> " oportunidad.", 
		
		//REGISTER
		"ACCOUNT_REGISTER" 						=> "Regístrese para este estudio", 
		"ACCOUNT_YOUR_NAME"						=> "Su Nombre",
		"ACCOUNT_FIRST_NAME" 					=> "Nombre",
		"ACCOUNT_LAST_NAME" 					=> "Apellido",
		"ACCOUNT_YOUR_EMAIL" 					=> "Email / Correo electrónico",
		"ACCOUNT_EMAIL_ADDRESS" 				=> "Email / Correo electrónico",
		"ACCOUNT_REENTER_EMAIL" 				=> "Nuevamente entre su correo electrónico",
		"ACCOUNT_EMAIL_ADDRESS_OR_USERNAME" 	=> "Email Address or Username",
		"ACCOUNT_PARTICIPANT_ID"				=> "ID de participante",
		"ACCOUNT_USERNAME"						=> "Username",
		"ACCOUNT_YOUR_LOCATION" 				=> "Lugar de vivienda",
		"ACCOUNT_CITY" 							=> "Ciudad",
		"ACCOUNT_ZIP" 							=> "Código Postal",
		"ACCOUNT_ALREADY_REGISTERED" 			=> "¿Se ha registrado anteriormente?",
		"ACCOUNT_BIRTH_YEAR" 					=> "¿En qué año nació?",
		"ACCOUNT_18_PLUS" 						=> "¿Tiene 18 años o más?",
		"ACCOUNT_USA_CURRENT" 					=> "¿Vive en los Estados Unidos?",
		"ACCOUNT_AGREE" 						=> "Al oprimir el botón de Entregar, estoy de acuerdo a ser contactado sobre estudios relacionados a WELL Bien Para Vida y recibir más información.",
		"ACCOUNT_ELITE_THANKS" 					=> "¡Gracias por ser uno de los primeros 500 participantes. ¡La data que recolectamos nos ayudará a mejorar el bienestar de todos! ¡Demuestre su logro con orgullo! ",
		"STEP_REGISTER"							=> "Registrarse",
		"STEP_VERIFY"							=> "Verificar Email",
		"STEP_CONSENT"							=> "Consentimiento",
		"STEP_SECURITY"							=> "Seguridad",

		"ACCOUNT_NEW_PASSWORD" 					=> "Contraseña Nueva",
		"ACCOUNT_PASSWORD" 						=> "Contraseña",
		"ACCOUNT_PASSWORD_AGAIN" 				=> "Contraseña",

		"ACCOUNT_LOGIN_PAGE" 					=> "Entrar",
		"ACCOUNT_REGISTER_PAGE" 				=> "Registrarse",

		"REGISTER_STUDY" 						=> "Regístrese para el estudio",
		"REGISTER_TOKEN_INVALID_1" 				=> "El código de activación es inválido o ha expirado. Esto puede surgir sí ha regenerado un código nuevo pero siguió el link de un mensaje antiguo.",
		"REGISTER_TOKEN_INVALID_2" 				=> "Código de activación inválido <br><a class='alink' href='login.php'>Oprima aquí</a> y seleccione 'Olvidé Contraseña’ para obtener un nuevo código.",

		//LOGIN
		"ACCOUNT_LOGIN_CONTINUE" 				=> "Por favor Entre para continuar",
		"ACCOUNT_LOGIN_NOW" 					=> "Entre Ahora",
		"ACCOUNT_NEXT_STEP" 					=> "Próximo Paso",

		//CONSENT
		"IRB_ONLY" 								=> "Solo para uso del IRB",
		"IRB_EXPIRATION"						=> "Fecha de Expiración",
		"CONSENT_BULLET_1" 						=> "Necesitamos su permiso antes de hacer preguntas, por favor lea el documento de consentimiento informado",
		"CONSENT_BULLET_2" 						=> "La encuesta inicial tomará 20-30 minutos para completar – pero no tiene que llenarla toda a la vez",
		"CONSENT_BULLET_3" 						=> "Nos comunicaremos con usted cada varios meses",
		"CONSENT_BULLET_4" 						=> "Vamos a añadir nuevas encuestas, materiales y contenido y le invitaremos a participar a través del tiempo",
		"CONSENT_WELCOME" 						=> "¡BIENVENIDOS!",
		"CONSENT_CONTACT" 						=> "PARA PREGUNTAS SOBRE EL ESTUDIO, COMUNIQUESE CON el Director del Protocolo, John Ioannidis al (650) 725-5465.",
		"CONSENT_I_AGREE" 						=> "Estoy de acuerdo",
		"CONSENT_PRINT" 						=> "Imprimir",

		//FORGOT PASSWORD AND ACCOUNT SETUP
		"FORGOTPASS" 							=> "¿Olvidó su contraseña?",
		"FORGOTPASS_RESET" 						=> "Reiniciar Contraseña",
		"FORGOTPASS_RESET_FORM" 				=> "Formulario para reiniciar contraseña",
		"FORGOTPASS_PLEASE_ANSWER" 				=> "Por favor conteste las preguntas de seguridad.",
		"FORGOTPASS_RECOVERY_ANSWER" 			=> "Contestación para recuperar contraseña",
		"FORGOTPASS_SEC_Q" 						=> "Pregunta de Seguridad",
		"FORGOTPASS_ANSWER_QS" 					=> "Contestar mis preguntas de seguridad",
		"FORGOTPASS_EMAIL_ME" 					=> "Envíame un link para reiniciar contraseña",
		"FORGOTPASS_RECOVERY_METHOD" 			=> "Seleccionar método de recuperación",
		"FORGOTPASS_BEGIN_RESET" 				=> "Entre su correo electrónico para reiniciar contraseña",
		"FORGOTPASS_SUGGEST"					=> "Oprima '¿Olvidó Contraseña?' para reiniciar su contraseña. O <a href='register.php'>regístrese aquí</a>.",
		"FORGOTPASS_INVALID_TOKEN"				=> "Código inválido.",
		"FORGOTPASS_REQUEST_EXISTS"				=> "Enviamos un mensaje a su correo electrónico hace %m1% minutos.<br>Por favor verifique su correo electrónico o intente nuevamente más tarde.",
		"FORGOTPASS_REQUEST_SUCCESS"			=> "El proceso para reiniciar su contraseña ha comenzado. <br> Por favor verifique su correo electrónico para instrucciones.",
		"FORGOTPASS_CREATED" 					=> "Contraseña Creada",
		"FORGOTPASS_UPDATED" 					=> "Contraseña Actualizada",
		"FORGOTPASS_INVALID_VALUE" 				=> "Contraseña inválida, reinicie los valores para la pregunta",
		"FORGOTPASS_Q_UPDATED" 					=> "¡Las preguntas para recuperar su contraseña ahora están actualizadas!",
		"FORGOTPASS_SEC_Q_SETUP" 				=> "Por favor establezca su contraseña<br> y sus preguntas de seguridad",
		"FORGOTPASS_SEC_Q_ANSWERS" 				=> "Para poder ayudar a recuperar su contraseña olvidada o perdida, por favor provea contestaciones a las siguientes preguntas de seguridad.",
		"FORGOTPASS_CHOSE_QUESTION" 			=> "Seleccione una pregunta de la lista",
		"FORGOTPASS_WRITE_CUSTOM_Q" 			=> "Escriba una pregunta de seguridad personalizada",

		//MAIL
		"MAIL_ERROR"							=> "Error intentando enviar correo electrónico, contacte su administrador de servidor de correo electrónico",
		"MAIL_TEMPLATE_BUILD_ERROR"				=> "Error desarrollando plantilla de correo electrónico",
		"MAIL_TEMPLATE_DIRECTORY_ERROR"			=> "No se puede abrir directorio de plantillas de correo electrónico. Quizás intente establecer el directorio de correo a %m1%",
		"MAIL_TEMPLATE_FILE_EMPTY"				=> "Archivo de plantilla está vacío… nada para enviar",

		//Miscellaneous
		"GENERAL_YES" 							=> "Sí",
		"GENERAL_NO" 							=> "No",
		"GENERAL_BACK" 							=> "Regresar",
		"GENERAL_NEXT" 							=> "Próximo",
		"GENERAL_SUBMIT" 						=> "Entregar",
		"CONFIRM"								=> "Confirmar",
		"ERROR"									=> "Error",

		//REPORT
		"OVERALL_WELL_BEING_SCORE" 				=> "Resultados Generales de WELL-Bienestar",
		"RADAR_CHART_MEANING" 					=> "¿Qué significa este gráfico?",
		"RADAR_CHART_REVIEW"					=> "*Para revisar su puntaje para cada dominio individual, pase el cursor sobre el punto de datos con el mouse. Hay 10 dominios del bienestar. La puntuación para cada dominio puede variar de 0-10. Una puntuación más baja en un dominio puede indicar: (1) que el dominio no es importante o no tiene un valor especial para usted, o (2) tiene una oportunidad de crecimiento en ese dominio.   La puntuación total es la suma de los 10 dominios. El objetivo no es optimizar su puntaje total, pero optimizar sus puntajes en los dominios de importancia para usted.",
		"RADAR_CHART_DOMAINS"					=> "\"Los dominios que están en texto verde son dominios que usted indicó que son los más importantes para su bienestar. Intente optimizar tu puntuación WELL en esos dominios. Los dominios que están en texto rojo son dominios que son menos importantes para su bienestar. No es tan importante para usted optimizar su puntaje en esas áreas.\"",
        "SCORE_NA" 					            => "A WELL Score was not calculated because some required questions in the survey were not answered.  Please be sure to fully complete next year's WELL survey to receive your WELL Score.",

        "DOMAIN_ORDER_INSTRUCTION"				=> "Por favor, piense en lo importante que es cada uno de los 10 dominios de bienestar para su bienestar personal. Luego arrastre y suelte los tres dominios más importantes y los tres dominios menos importantes en los cuadros correspondientes en orden de importancia.",
		"MOST_IMPORTANT"						=> "3 Dominios Más Importantes",
		"LEAST_IMPORTANT"						=> "3 Dominios Menos Importantes",
		"TEN_DOMAINS"							=> "10 Dominios del Bienestar",
		"DOMAIN_SAVED"							=> "Gracias por clasificar los dominios de bienestar. Después de terminar la encuesta, vaya a la pestaña de informes para ver cómo se comparan las clasificaciones de sus dominios con las puntuaciones de sus dominios.",
		"DOMAIN_SAVED_REDIRECT"					=> "Gracias por clasificar los dominios de bienestar. Después de terminar este siguiente conjunto de preguntas, vaya a la pestaña de informes para ver cómo se comparan las clasificaciones de sus dominios con las puntuaciones de sus dominios. Redireccionando a la encuesta de Bienestar en 3...2..1",
		"DOMAIN_SC_DESC" 						=> "Relaciones positivas o negativas con los demás y cómo influencian su bienestar.",
		"DOMAIN_LB_DESC" 						=> "Comportamientos de estilo de vida que pueden influir en su bienestar, tales como: dieta; actividad física; el sueño; el consumo de tabaco, alcohol y marihuana; y otras formas en que las personas se cuidan a sí mismas..",
		"DOMAIN_SR_DESC"						=> "Poder de Recuperación: Capacidad para adaptarse al cambio y recuperarse después de las dificultades. Estrés: Sentimientos de sobrecarga y una incapacidad para equilibrar o administrar tareas.",
		"DOMAIN_EE_DESC" 						=> "¿Con qué frecuencia siente emociones tanto placenteras como desagradables?",
		"DOMAIN_PM_DESC" 						=> "Tener la sensación de que los aspectos de su vida proporcionan propósito y significado, es decir, metas, sueños, ser parte de algo más grande que usted.",
		"DOMAIN_PH_DESC" 						=> "Percepción de su propio estado de salud, es decir, niveles de energía, capacidad para resistir enfermedades, estado físico, experiencia de dolor.",
		"DOMAIN_SS_DESC" 						=> "La medida en que usted siente que se conoce a sí mismo, puede expresar su verdadera persona, tener confianza en sí mismo y sentirse bien con respecto a quién es usted.",
		"DOMAIN_RS_DESC" 						=> "La medida en que las creencias, prácticas, comunidades y tradiciones espirituales o religiosas son importantes en su vida.",
		"DOMAIN_FS_DESC" 						=> "Su percepción de tener suficiente dinero para satisfacer sus necesidades.",
		"DOMAIN_EC_DESC" 						=> "Tener oportunidades para crecer como persona y explorar nuevas experiencias y formas de pensar.",
        "RESULTS_SUMMARY"                       => "Results Summary",
        "PRINT"                                 => "Print",

		//DIALOG BOXES
		"WELL_CHALLENGE"                        => "WELL Challenge",
		"WELL_CHALLENGE_BODY1"                  => "You will recieve points and a background as reward for completing WELL-Challenges. View your rewards on the <a href='rewards.php'>Rewards Page</a>.",
		"WELL_CHALLENGE_BODY2"                  => "Click to continue to the WELL-Challenge.",
		"WELL_CHALLENGE_BTN"                    => "Go to WELL-Challenge",

		"WOF_UNLOCKED"                          => "Congrats, You've unlocked <br>The WELL OF FORTUNE game",
		"WOF_UNLOCKED_BODY"                     => "You will now be able to use the WELL points that you have earned to play <a href='game.php'>WELL OF FORTUNE</a> and earn more prizes!",
		"WOF_UNLOCKED_BTN"                      => "Go to WELL OF FORTUNE",


		"CANCEL"                                => "Cancel",
	));

	//DASHBOARD TRANSLATIONS
	$lang = array_merge($lang, array(
		 "WELL_FOR_LIFE" 							=> "WELL Bien Para Vida"
		,"MY_DASHBOARD" 							=> "Mis cuestionarios"
		,"CORE_SURVEYS" 							=> "Encuestas principales"
		,"COMPLETED_SURVEYS" 						=> "Encuestas Completadas"
		,"LOGOUT" 									=> "Cerrar sesión"
		,"MY_WELL"									=> "Mi WELL"
		,"MY_STUDIES"								=> "Mis estudios"
		,"MY_REPORTS"								=> "Mis Reportes"
		,"MY_ASSESSMENTS" 							=> "Mis Questionarios"
		,"NO_ASSESSMENTS"							=> "Aún no hay resultados disponibles.<br>Complete las encuestas suplementarias para ver sus comentarios personalizados de WELL."
		,"YOUR_ASSESSMENT"							=> "Sus Questionarios"
		,"MY_PROFILE" 								=> "Mi Perfil"
		,"CONTACT_US" 								=> "Contáctenos"
		,"GET_HELP" 								=> "Dónde obtener ayuda"
		,"GET_HELP_TEXT" 							=> "<p>Para una emergencia médica, llame al 911 o a su médico o proveedor de salud.</p><p>Para salud mental, por favor visite <a href='https://www.mentalhealth.gov/get-help/' class='offsite'>MentalHealth.gov</a>.</p>"
		,"QUESTION_FOR_WELL" 						=> "Preguntas para WELL"
		,"YOUVE_BEEN_AWARDED" 						=> "Usted se ha ganado"
		,"GET_WHOLE_BASKET" 						=> "¡Obtenga la canasta entera de frutas!"
		,"CONTINUE_SURVEY" 							=> "Continúe con el resto de la encuesta."
		,"CONGRATS_FRUITS" 							=> "¡Felicidades, usted ha recibido todas las frutas!  <br/><br/> Busque nuestras encuestas nuevas bajo 'Aprender Más'. <br><br/> Por ahora le invitamos a ver este video de la directora de WELL. <br/><br/>"
		,"CONGRATS_CERT" 							=> "Before we present you with your WELL score<br/> and completion certifcate.<br/> Please confirm your email address.<br><br>"
    	,"THANKS" 							        => "Thank you.<br/> For a more in-depth report of your WELL score,<br> visit the 'Reports' tab."
		,"FITNESS_BADGE" 							=> "¡Usted se ha ganado una medalla de salud física!"
		,"GET_ALL_BADGES" 							=> "Obtenga la canasta entera de frutas!"
		,"CONGRATS_ALL_FITNESS_BADGES"				=> "¡Felicidades, used ha recibido todas las medallas de salud física! <br/> Regrese pronto para la oportunidad de ganarse más premios!"
		,"DONE_CORE" 								=> "¡He terminado mi primera encuesta!"
		,"TAKE_BLOCK_DIET" 							=> "Complete la encuesta de dieta de Block, gratis para todos los participantes de WELL. Esta encuesta toma típicamente 30-50 minutos y le provee sugerencias instantáneamente."
		,"HOW_WELL_EAT" 							=> "¿Cuan bien come usted?"
		,"COMPLETE_CORE_FIRST" 						=> "Por favor complete las encuestas básicas primero"
		,"PLEASE_COMPLETE" 							=> "Por favor complete "
		,"WELCOME_TO_WELL" 							=> "<b>Bienvenidos</b> a WELL Bien Para Vida! <u>Oprima aquí</u> para comenzar su aventura con WELL…</a>"
		,"WELCOME_BACK_TO" 							=> "<b>Bienvenidos nuevamente </b> a WELL Bien Para Vida!</a>"
		,"REMINDERS" 								=> "Recordatorios"
		,"ADDITIONAL_SURVEYS" 						=> "Encuestas adicionales"
		,"SEE_PA_DATA" 								=> "¡Complete la sección 'Su actividad física' de la encuesta para ver sus datos representados aquí!"
		,"HOW_DO_YOU_COMPARE" 						=> "¿Cómo se compara con otros participantes?"
		,"SHORT_SCORE_OVER_TIME"					=> "Su Puntuación de WELL-Bienestar"
		,"OTHERS_WELL_SCORES"						=> "Otros Resultados de WELL a Lo Largo del Tiempo"
		,"OTHERS_SCORE"								=> "Promedio de los Participantes"
		,"USERS_SCORE"								=> "Tu Puntuación"
		,"HIGHER_WELLBEING"							=> "Mayor Bienestar"
		,"LOWER_WELLBEING"							=> "Bajo Bienestar"
		,"NOT_ENOUGH_USER_DATA" 					=> "Por favor complete las encuestas para calcular su puntuación."
		,"NOT_ENOUGH_OTHER_DATA" 					=> "No hay suficientes datos para calcular el promedio."
		,"SITTING" 									=> "Sentado(a)"
		,"WALKING" 									=> "Caminando"
		,"MODACT" 									=> "Actividad moderada"
		,"VIGACT" 									=> "Actividad vigorosa"
		,"NOACT" 									=> "Actividad Liviana o Ninguna Actividad"
		,"SLEEP" 									=> "Durmiendo"
		,"YOU_HOURS_DAY"							=> "Usted (Horas/Día)"
		,"AVG_ALL_USERS" 							=> "Promedio de todos los usuarios (Horas/Día)"
		,"HOW_YOU_SPEND_TIME" 						=> "Descripción de cómo usted pasa su tiempo cada día"
		,"SUNRISE" 									=> "Amanecer"
		,"SUNSET" 									=> "Atardecer"
		,"WIND" 									=> "Viento"
		,"DASHBOARD"								=> "Página Principal"
		,"WELCOME_BACK"								=> "¡Bienvenidos nuevamente!"
		,"SUBMIT"									=> "Entregar"
		,"SAVE_EXIT"								=> "Guardar y Salir"
		,"SUBMIT_NEXT"								=> "Entregar/Próximo"
		,"MAT_DATA_DISCLAIM" 						=> "La siguiente data ha sido preparada en parte utilizando información de estudios previos sobre salud cardio-respiratoria y estándares nacionales de salud. Estos resultados no deberían sustituir recomendaciones o sugerencias de su médico. Hable con su doctor antes de hacer cualquier cambio que podría afectar su salud."
		,"MAT_SCORE_40"								=> "En los próximos 4 años, personas con sus resultados tienen alta probabilidad (6.6 de 10) de perder la habilidad de hacer las actividades físicas que disfrutan y valoran. Sin embargo, hay muchas cosas que usted puede hacer para mejorar su habilidad física y capacidad funcional."
		,"MAT_SCORE_50"								=> "En los próximos 4 años, personas con sus resultados tienen probabilidad (5.2 de 10) de perder la habilidad de hacer las actividades físicas que disfrutan y valoran. Sin embargo, hay muchas cosas que usted puede hacer para mejorar su habilidad física y capacidad funcional."
		,"MAT_SCORE_60"								=> "En los próximos 4 años, personas con sus resultados tienen alguna probabilidad (3.5 de 10) de perder la habilidad de hacer las actividades físicas que disfrutan y valoran. Sin embargo, hay muchas cosas que puede hacer para mejorar su habilidad física y capacidad funcional.Las personas con sus resultados tienen baja probabilidad de perder la habilidad de poder hacer actividades físicas que disfrutan o valoran. ¡Siga con el buen trabajo e intente mantener su capacidad funcional!"
    	,"MAT_SCORE_70"								=> "Es poco probable que las personas con su puntaje pierdan la capacidad de hacer cosas activas que ellos disfrutan o valoran. ¡Sigue con el buen trabajo y trata de mantener tu capacidad funcional!"

    	,"TCM_POSITIVE"								=> "Positivo"
		,"TCM_NEGATIVE"								=> "Negativo"
		,"TCM_ESSENTIALLY_POS"						=> "Tendencia (Esencialmente) Positiva"

		,"PROFILE_JOINED"							=> "Miembro desde"
		,"PROFILE_NICKNAME"							=> "Apodo"
		,"ACCOUNT_MIDDLE_NAME"						=> "Segundo Nombre"
		,"PROFILE_CONTACT_NAME"						=> "Nombre de Contacto"
		,"PROFILE_CONTACT_PHONE"					=> "Número de Teléfono de Contacto"
		,"PROFILE_STREET_ADDRESS"					=> "Dirección"
		,"PROFILE_APARTMENT"						=> "Apt"
		,"ACCOUNT_STATE"							=> "Estado"
		,"EDIT_PROFILE"								=> "Editar"
		,"PROFILE_EDIT"								=> "Perfil"

		,"NAV_HOME" 								=> "Hogar"
		,"NAV_RESOURCES"							=> "Recursos"
		,"NAV_REWARDS"								=> "Recompensas"
		,"NAV_REPORTS"								=> "Reportes"
		,"NAV_GAME" 								=> "Juego"
		,"NAV_ACTIVITY" 							=> "Actividad"
		,"NAV_CHALLENGES" 							=> "WELL Challenges"

		,"DOMAIN_RANKING" 							=> "Clasifica tus dominios"
		,"DOMAIN_RANKING_PROMPT" 					=> "¡Estás a medio camino! Antes de continuar, tómese un momento para completar la actividad Dominios del bienestar ... Redireccionando en 3 ... 2 ... 1 ..."
		,"YOUR_DOMAIN_RANKING" 						=> "Clasificación en Orden de Sus Dominios"
		,"READ_MORE"								=> "Leer Más"
		,"GO_TO_SURVEY"								=> "Ir a la Encuesta"
		,"ENHANCE_WELLBEING"						=> "¿Cómo puedo mejorar mi bienestar?"
		,"BRIEF_WELL" 								=> "Escala WELL Bien Para Vida Breve"
		,"STANFORD_WELL" 							=> "Escala Stanford WELL Bien Para Vida"
		,"CERTIFICATES" 							=> "Certificados"
		,"WELL_SCORE_NA"							=> "<p>Lo sentimos, no teníamos datos suficientes para calcular el puntaje de la escala de Well Bien Para Vida para este año.</p><p>Please be sure to fully complete the surveys in the future.</p>"
		,"BRIEF_SCORE"								=> "Your %m1% Brief* WELL for Life Scale Score <b>%m2%/100</b> "
		,"BRIEF_SORRY_NA" 							=> "<p>Sorry, we did not have enough data to calculate a Brief Well for Life Scale Score for this year.</p><p>Por favor, asegúrese de completar toda la encuesta en el futuro.</p>"
		,"BREIF_DISCLAIMER" 						=> "<i>*Debido a la escala reducida, solo podemos proporcionar un puntaje de bienestar general, no puntajes de dominios individuales. Asegúrese de tomar la escala normal de WELL Bien Para Vida el próximo año para obtener un informe completo. Si desea comparar sus puntajes generales de bienestar, use la función 'Comparar' que se encuentra en la pestaña de Informes.</i>"
		,"RESOURCE_CREATIVITY" 						=> "Exploración y Creatividad"
		,"RESOURCE_LIFESTYLE" 						=> "Estilo de Vida y Practicas Diarias"
		,"RESOURCE_SOCIAL" 							=> "Conectividad Social"
		,"RESOURCE_STRESS" 							=> "Estrés y Poder de Recuperación"
		,"RESOURCE_EMOTIONS" 						=> "Salud Mental y Emocional"
		,"RESOURCE_SELF" 							=> "Sentido de Sí Mismo"
		,"RESOURCE_PHYSICAL"						=> "Salud Física"
		,"RESOURCE_PURPOSE" 						=> "Propósito Y Significado"
		,"RESOURCE_FINANCIAL" 						=> "Finanzas"
		,"RESOURCE_RELIGION" 						=> "Espiritualidad y Religiosidad"
		,"RESOURCE_NONE"							=> "No Hay Recursos Actuales"
		,"YOUR_MET"									=> "Su actividad física MET-minutos/semana puntuación es"
		,"CERT_DL"									=> "Haga clic aquí para descargar su certificado!"
		,"WELL_SCORE_YEAR"							=> "Tu puntuación de WELL para %m1% es <b class='wellscore'>%m2%</b>"
		,"UO1_REMINDER"								=> "Por favor complete las encuestas de Medicina Tradicional China y SLEEP"
		,"MYWELLTREE"								=> "Mi Árbol Well Tree"
	));

	$template_security_questions = array(
			'concert'	=> '¿Cual fue su primer concierto?',
			'cartoon'	=> '¿Cual fue su serie de muñequitos preferida como niño(a)?',
			'reception'	=> '¿Dónde tuvo su recepción de boda?',
			'sib_nick'	=> '¿Cual era el apodo de su hermano(a) mayor?',
			'street'	=> '¿En qué calle vivía usted en 3er grado?',
			'pet'		=> '¿Cual era el nombre de su primera mascota?',
			'parents'	=> '¿Cómo se llama la ciudad donde sus padres se conocieron?',
			'grammie'	=> '¿Cual es el apodo de su abuela materna?',
			'boss'		=> '¿Cómo se llamaba su primer supervisor o supervisora?',
			'sib_mid'	=> '¿Cual es el segundo nombre de su hermano(a) mayor?',
			'custom'	=> ''
		);

	$websiteName = "WELL Bien Para Vida";
?>
