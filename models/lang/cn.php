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
		"ACCOUNT_SPECIFY_F_L_NAME" 				=> "请输入您的名字和姓氏",
		"ACCOUNT_SPECIFY_USERNAME" 				=> "请输入您的用户帐号",
		"ACCOUNT_SPECIFY_PASSWORD" 				=> "请输入您的密码",
		"ACCOUNT_SPECIFY_EMAIL"					=> "请输入您的电子邮件地址",
		"ACCOUNT_INVALID_EMAIL"					=> "无效的电子邮件地址。",
		"ACCOUNT_EMAIL_MISMATCH"				=> "电子邮件必须符合。",
		"ACCOUNT_USER_OR_PASS_INVALID"			=> "电子邮件和/或密码不被识别。",
		"ACCOUNT_PASS_MISMATCH"					=> "密码必须符合。",
		"ACCOUNT_EMAIL_IN_USE_ACTIVE"			=> "电子邮件 %m1% 已在使用中。如果您忘记了密码，您可以重新设定于 <a href='login.php'>登录表单</a>",
		"ACCOUNT_NEW_ACTIVATION_SENT"			=> "感谢您注册WELL for Life计划！我们已向您的电子邮件发送了帐户激活链接。请检查您的电子邮件，并点击里面的链接。如果您在1小时内没有收到电子邮件，请通过wellforlife@stanford.edu与我们联系。",
		"ACCOUNT_SPECIFY_NEW_PASSWORD"			=> "请输入您的新密码",	
		"ACCOUNT_NOT_YET_ELIGIBLE"				=> "感谢您对WELL for Life计划的关注！您目前没有资格参加。%m1% 当计划扩大时，我们将与您联系关于WELL for Life相关研究和信息。",
		"ACCOUNT_NEED_LOCATION"					=> "请输入您的邮政编码或城市。",
		"ACCOUNT_TOO_YOUNG"						=> "您尚未年满18岁。",
		"ACCOUNT_NOT_IN_USA"					=> "本研究计划仅适用于居住在美国的参与者。",
		"ACCOUNT_NOT_IN_ELIGIBLE_LOCATION" 		=> "本研究计划仅适用于居住在美国的参与者。",
		"ACTIVATION_MESSAGE"					=> "您需要首先激活您的帐户，然后才能登录。请按照以下链接激活您的帐户。 \n\n%m1%register.php?activation=%m2%",							
		"ACCOUNT_ERROR_TRY_AGAIN"				=> "再试一次... ", 
		"ACCOUNT_ERROR_ATTEMPTS"				=> " 剩余尝试次数。",
		"ACCOUNT_ERROR_ATTEMPT"					=> " 剩余尝试次数。", 

		//REGISTER
		"ACCOUNT_REGISTER" 						=> "注册本研究计划",
		"ACCOUNT_YOUR_NAME"						=> "您的姓名",
		"ACCOUNT_FIRST_NAME" 					=> "英文名字",
		"ACCOUNT_LAST_NAME" 					=> "英文姓氏",
		"ACCOUNT_YOUR_EMAIL" 					=> "电子邮件",
		"ACCOUNT_EMAIL_ADDRESS" 				=> "电子邮件地址",
		"ACCOUNT_EMAIL_ADDRESS_OR_USERNAME" 	=> "电子邮件地址或用户帐号",
		"ACCOUNT_USERNAME"						=> "Username",
		"ACCOUNT_PARTICIPANT_ID"				=> "参与者编号",
		"ACCOUNT_REENTER_EMAIL" 				=> "重新输入电子邮件",
		"ACCOUNT_YOUR_LOCATION" 				=> "您居住的地区",
		"ACCOUNT_CITY" 							=> "英文城市",
		"ACCOUNT_ZIP" 							=> "邮政编码",
		"ACCOUNT_ALREADY_REGISTERED" 			=> "已经注册过？",
		"ACCOUNT_BIRTH_YEAR" 					=> "你的出生年份是？",
		"ACCOUNT_18_PLUS" 						=> "你满18岁吗？",
		"ACCOUNT_USA_CURRENT" 					=> "你现在住在美国吗？",
		"ACCOUNT_AGREE" 						=> "点击送出按钮,我同意被联系有关WELL for Life相关研究和信息。",
		"ACCOUNT_ELITE_THANKS" 					=> "感谢您成为我们首批500名参与者之一。我们收集的数据将帮助我们改善我们的健康！自豪地展示你的丝带！",
		"STEP_REGISTER"							=> "注册",
		"STEP_VERIFY"							=> "验证邮件",
		"STEP_CONSENT"							=> "同意",
		"STEP_SECURITY"							=> "安全性",

		"ACCOUNT_NEW_PASSWORD" 					=> "新密码",
		"ACCOUNT_PASSWORD" 						=> "密码",
		"ACCOUNT_PASSWORD_AGAIN" 				=> "密码再一次",

		"ACCOUNT_LOGIN_PAGE" 					=> "登录页面",
		"ACCOUNT_REGISTER_PAGE" 				=> "注册页面",
		
		"REGISTER_STUDY" 						=> "注册本研究计划",
		"REGISTER_TOKEN_INVALID_1" 				=> "提供的电子邮件激活链接无效或已过期。",
		"REGISTER_TOKEN_INVALID_2" 				=> "电子邮件激活链接无效 <br><a class='alink' href='login.php'>点击此处</a> 并选择“忘记密码”获取新的链接。",

		//LOGIN
		"ACCOUNT_LOGIN_CONTINUE" 				=> "请登录完再继续",
		"ACCOUNT_LOGIN_NOW" 					=> "登录",
		"ACCOUNT_NEXT_STEP" 					=> "下一步",
		
		//CONSENT
		"IRB_ONLY" 								=> "IRB Use Only",
		"IRB_EXPIRATION"						=> "Expiration Date",
		"CONSENT_BULLET_1" 						=> "我们需要您的许可，我们才能向您提出任何问题，因此请阅读以下同意文件",
		"CONSENT_BULLET_2" 						=> "初步问卷需时30分钟完成。但你不需要一次性填写完。",
		"CONSENT_BULLET_3" 						=> "每隔几个月我们会联系您有关WELL的最新资讯。",
		"CONSENT_BULLET_4" 						=> "我们将会持续加入新的问卷，材料和内容，并邀请您参与。",
		"CONSENT_WELCOME" 						=> "欢迎加入!",
		"CONSENT_CONTACT" 						=> "如您对本研究有任何问题，请联系总监 Dr. Ann Hsing at annhsing@stanford.edu。",
		"CONSENT_I_AGREE" 						=> "我同意",
		"CONSENT_PRINT" 						=> "列印",
		
		//FORGOT PASSWORD AND ACCOUNT SETUP
		"FORGOTPASS" 							=> "忘记密码？",
		"FORGOTPASS_RESET" 						=> "重设密码",
		"FORGOTPASS_RESET_FORM" 				=> "密码重新设定表单",
		"FORGOTPASS_PLEASE_ANSWER" 				=> "請回答您设定的安全問題。",
		"FORGOTPASS_RECOVERY_ANSWER" 			=> "恢復原密码的答案。",
		"FORGOTPASS_SEC_Q" 						=> "安全性问题。",
		"FORGOTPASS_ANSWER_QS" 					=> "回答设订的安全性问题。",
		"FORGOTPASS_EMAIL_ME" 					=> "請送我密碼重新设定的電子郵件。",
		"FORGOTPASS_RECOVERY_METHOD" 			=> "选择回复方法",
		"FORGOTPASS_BEGIN_RESET" 				=> "输入电子邮件以开始密码重新设置",
		"FORGOTPASS_SUGGEST"					=> "点击 “忘记密码？” 来重新设置密码。 或<a href=\"register.php\">注册加入</a>.",
		"FORGOTPASS_INVALID_TOKEN"				=> "无效链接",
		"FORGOTPASS_REQUEST_EXISTS"				=> "忘记的密码授权电子邮件已于 %m1% 分钟前发送。<br>请检查您的电子邮件或稍后再试。",
		"FORGOTPASS_REQUEST_SUCCESS"			=> "已启动密码重设程序。<br>已啟動密碼重設程序。 <br>請查看您的電子郵件以了解後續步驟。",
		"FORGOTPASS_CREATED" 					=> "Password Created",
		"FORGOTPASS_UPDATED" 					=> "密码更新",
		"FORGOTPASS_INVALID_VALUE" 				=> "密码重置值无效",
		"FORGOTPASS_Q_UPDATED" 					=> "恢复密码问题更新！",
		"FORGOTPASS_SEC_Q_SETUP" 				=> "密码设置和安全性问题",
		"FORGOTPASS_SEC_Q_ANSWERS" 				=> "为了帮助您恢复丢失或忘记的密码，请提供以下安全问题的答案。",
		"FORGOTPASS_CHOSE_QUESTION" 			=> "请从列表中选择一个问题",
		"FORGOTPASS_WRITE_CUSTOM_Q" 			=> "编写自定安全性问题",

		//MAIL
		"MAIL_ERROR"							=> "尝试邮件时出错，请与网站管理员联系",
		"MAIL_TEMPLATE_BUILD_ERROR"				=> "建构电子邮件范本时出错",
		"MAIL_TEMPLATE_DIRECTORY_ERROR"			=> "无法打开邮件范本目录。也许尝试设置邮件目录于 %m1%",
		"MAIL_TEMPLATE_FILE_EMPTY"				=> "范本文件是空的...没有发送任何文件",

		//Miscellaneous
		"GENERAL_YES" 							=> "是",
		"GENERAL_NO" 							=> "否",
		"GENERAL_BACK" 							=> "回上一步",
		"GENERAL_NEXT" 							=> "下一步",
		"GENERAL_SUBMIT" 						=> "送出",
		"CONFIRM"								=> "确认",
		"ERROR"									=> "错误",

		//REPORT
		"OVERALL_WELL_BEING_SCORE" 				=> "Overall WELL-Being Score",
		"RADAR_CHART_MEANING" 					=> "What does this chart mean?",
		"RADAR_CHART_REVIEW"					=> "*To review your score for each individual domain, hover over the data point with your mouse. There are 10 constituent domains of well-being. The score for each domain can range from 0-10. A lower score in a domain can indicate: (1) that domain is not of importantance or of particular value to you, or (2) you have an opportunity for growth in that domain.   The total score is the sum of the 10 domains. The goal is not to optimize your total score, but to optimize your scores on the domains of importance to you.",
		"RADAR_CHART_DOMAINS"					=> "\"Domains that are in green text are domains that you indicated are most important to your well-being. Try to optimize your WELL score in those domains. Domains that are in red text are domains that are less important to your well-being. It is not as important for you to optimize your score in those areas.\"",
        "SCORE_NA" 					            => "A WELL Score was not calculated because some required questions in the survey were not answered.  Please be sure to fully complete next year's WELL survey to receive your WELL Score.",

        "DOMAIN_ORDER_INSTRUCTION"				=> "Please think about how important each of the 10 domains of well-being are to your personal well-being. Then drag-and-drop the three most important domains and the three least important domains into the appropriate boxes in order of importance.",
		"MOST_IMPORTANT"						=> "3 Most Important Domains",
		"LEAST_IMPORTANT"						=> "3 Least Important Domains",
		"TEN_DOMAINS"							=> "10 Domains of Well-Being",
		"DOMAIN_SAVED"							=> "Thank you for ranking the well-being domains. After you finish the survey go to the reports tab to see how your domain rankings compare to your domain scores.",
		"DOMAIN_SAVED_REDIRECT"					=> "Thank you for ranking the well-being domains. After you finish this next set of questions go to the reports tab to see how your domain rankings compare to your domain scores. Redirecting back to Well-Being survey in 3...2..1",
		"DOMAIN_SC_DESC" 						=> "Positive or negative relationships with others and how they influence your well-being.",
		"DOMAIN_LB_DESC" 						=> "Lifestyle behaviors that can influence your well-being such as: diet; physical activity; sleep; the use of tobacco, alcohol, and marijuana; and other ways people take care of themselves.",
		"DOMAIN_SR_DESC"						=> "Resilience: Ability to adapt to change and bounce back after hardship. Stress: Feelings of overload and an inability to balance or manage tasks.",
		"DOMAIN_EE_DESC" 						=> "How often you experience both pleasant and unpleasant emotions.",
		"DOMAIN_PM_DESC" 						=> "Having a sense that aspects of your life provide purpose and meaning, i.e. goals, dreams, being part of something larger than yourself.",
		"DOMAIN_PH_DESC" 						=> "Perception of your own health status, i.e. energy levels, ability to resist illness, physical fitness, experience of pain.",
		"DOMAIN_SS_DESC" 						=> "The extent to which you feel you know yourself, can express your true self, have self-confidence and feel good about who you are.",
		"DOMAIN_RS_DESC" 						=> "The extent to which spiritual and religious beliefs, practices, communities and traditions are important in your life.",
		"DOMAIN_FS_DESC" 						=> "Your perception of having enough money to meet your needs.",
		"DOMAIN_EC_DESC" 						=> "Having opportunities to grow as a person and to explore new experiences and ways of thinking.",
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
		 "WELL_FOR_LIFE" 							=> "WELL人生"
		,"MY_DASHBOARD" 							=> "信息中心"
		,"CORE_SURVEYS" 							=> "我的问卷"
		,"COMPLETED_SURVEYS" 						=> "已完成的问卷"
		,"LOGOUT" 									=> "登出"
		,"MY_WELL"									=> "我的幸福感"
		,"MY_STUDIES"								=> "参与研究"
		,"MY_REPORTS"								=> "我的报告"
		,"MY_ASSESSMENTS" 							=> "我的报告"
		,"NO_ASSESSMENTS"							=> "请先完成附加问卷，即可查看您的评估报告"
		,"YOUR_ASSESSMENT"							=> "您的评估报告"
        ,"MY_PROFILE" 								=> "个人资料"
		,"CONTACT_US" 								=> "联系我们"
		,"GET_HELP" 								=> "需要帮助"
		,"GET_HELP_TEXT" 							=> "<p>对于医疗紧急情况，请致电911或您的医疗保健提供者。</p><p>对于精神健康问题，请参考 <a href=\"https://www.mentalhealth.gov/get-help/\" class='offsite'>MentalHealth.gov</a>.</p>"
		,"QUESTION_FOR_WELL" 						=> "对我们有疑问"
		,"YOUVE_BEEN_AWARDED" 						=> "你已经获得了"
		,"GET_WHOLE_BASKET" 						=> "获得整篮水果！"
		,"CONTINUE_SURVEY" 							=> "继续完成剩下的问卷。"
		,"CONGRATS_FRUITS" 							=> "恭喜，你得到了所有的水果！ <br/><br/>"
    	,"CONGRATS_CERT" 							=> "Before we present you with your WELL score<br/> and completion certifcate.<br/> Please confirm your email address.<br><br>"
    	,"THANKS" 							        => "Thank you.<br/> For a more in-depth report of your WELL score,<br> visit the 'Reports' tab."
   	 	,"FITNESS_BADGE" 							=> "您已获得健身徽章"
		,"GET_ALL_BADGES" 							=> "获得所有的健身徽章！"
		,"CONGRATS_ALL_FITNESS_BADGES"				=> "恭喜，你已获得所有的健身徽章！ <br/>请稍后再回来取得新的奖励！"
		,"DONE_CORE" 								=> "完成所有主要问卷！"
		,"TAKE_BLOCK_DIET" 							=> "所有WELL参与者可免费参与饮食评估。此问卷通常需要30-50分钟完成，并提供即时评估报告。"
		,"HOW_WELL_EAT" 							=> "您吃得健康吗？"
		,"COMPLETE_CORE_FIRST" 						=> "请先完成主要问卷"
		,"PLEASE_COMPLETE" 							=> "请完成 "
		,"WELCOME_TO_WELL" 							=> "<b>欢迎</b>参与WELL人生研究计划！<u>点击此处</u>开始您的幸福旅程！</a>"
		,"WELCOME_BACK_TO" 							=> "<b>欢迎回到</b>WELL人生!</a>"
		,"REMINDERS" 								=> "溫馨提醒"
		,"ADDITIONAL_SURVEYS" 						=> "附加问卷"
		,"SEE_PA_DATA" 								=> "填写问卷的 “您的身体活动” 部分，可看到您与其他参与者的数据比较图表！"
		,"HOW_DO_YOU_COMPARE" 						=> "你与其他参与者比较图表"
		,"SHORT_SCORE_OVER_TIME"					=> "您的幸福感指数"
		,"OTHERS_WELL_SCORES"						=> "您过去几年的幸福感指数"
		,"OTHERS_SCORE"								=> "参与者平均分数"
		,"USERS_SCORE"								=> "您的分数"
		,"HIGHER_WELLBEING"							=> "高的幸福感指数"
		,"LOWER_WELLBEING"							=> "低的幸福感指数"
		,"NOT_ENOUGH_USER_DATA" 					=> "请完成问卷才能计算您的幸福感指數"
		,"NOT_ENOUGH_OTHER_DATA" 					=> "没有足够的数据来计算平均值。"
		,"SITTING" 									=> "坐着"
		,"WALKING" 									=> "走路"
		,"MODACT" 									=> "中度身体活动"
		,"VIGACT" 									=> "重度身体活动"
		,"NOACT" 									=> "轻度/无活动"
		,"SLEEP" 									=> "睡眠"
		,"YOU_HOURS_DAY"							      =>  "您（小时/天）"
		,"AVG_ALL_USERS" 							=> "平均所有参与者（小时/天）"
		,"HOW_YOU_SPEND_TIME" 						=> "您每天如何分配时间"
		,"SUNRISE" 									=> "日出"
		,"SUNSET" 									=> "日落"
		,"WIND" 									=> "有风"
		,"DASHBOARD"								=> "信息中心"
		,"WELCOME_BACK"								=> "欢迎回来"
		,"SUBMIT"									=> "送出"
		,"SAVE_EXIT"								=> "储存并退出"
		,"SUBMIT_NEXT"								=> "下一步"
		,"MAT_DATA_DISCLAIM" 						=> "以下数据部分是参考心肺健康和国家健康标准的研究而准备的。这些结果不能替代医疗保健提供者的建议。在做出任何可能影响您健康的改变之前，请咨询您的医生。"
		,"MAT_SCORE_40"								=> "在接下来的4年中，和你的分数相同的人（10分中有6.6分）可能失去做他们喜欢或珍惜的活动的能力。然而，有很多方法你可以做，以提高你的功能性能力。"
		,"MAT_SCORE_50"								=> "在接下来的4年中，和你的分数相同的人（10分中有5.2分）可能失去做他们喜欢或珍惜的活动的能力。然而，有很多方法你可以做，以提高你的功能性能力。"
		,"MAT_SCORE_60"								=> "在接下来的4年中，和你的分数相同的人（10分中有3.5分）可能失去做他们喜欢或珍惜的活动的能力。然而，有很多方法你可以做，以提高你的功能性能力。"
		,"MAT_SCORE_70"								=> "在接下来的4年中，和你的分数相同的人应该不会失去做他们喜欢或珍惜的活动的能力。继续您的努力，并尝试保持您的功能性能力！"
		,"TCM_POSITIVE"								=> "肯定"
		,"TCM_NEGATIVE"								=> "没有"
		,"TCM_ESSENTIALLY_POS"						=> "倾向肯定"

		,"PROFILE_JOINED"							=> "已加入"
		,"PROFILE_NICKNAME"							=> "昵称"
		,"ACCOUNT_MIDDLE_NAME"						=> "中间名字"
		,"PROFILE_CONTACT_NAME"						=> "联络人姓名"
		,"PROFILE_CONTACT_PHONE"					=> "联络电话"
		,"PROFILE_STREET_ADDRESS"					=> "街道地址"
		,"PROFILE_APARTMENT"						=> "公寓号码"
		,"ACCOUNT_STATE"							=> "州"
		,"EDIT_PROFILE"								=> "编辑"
		,"PROFILE_EDIT"								=> "个人资料"

		,"NAV_HOME" 								=> "首页"
		,"NAV_RESOURCES"							=> "资源"
		,"NAV_REWARDS"								=> "Rewards"
		,"NAV_REPORTS"								=> "报告"
		,"NAV_GAME" 								=> "游戏"
		,"NAV_ACTIVITY" 							=> "活动"
	    ,"NAV_CHALLENGES" 							=> "WELL Challenges"

    	,"DOMAIN_RANKING" 							=> "Rank your domains"
		,"DOMAIN_RANKING_PROMPT" 					=> "You’re half way done! Before continuing, Please take a moment to complete the Domains of Well-Being Activity... Redirecting in 3 ... 2 ... 1 ..."
		,"YOUR_DOMAIN_RANKING" 						=> "Your well-being domain rankings"
		,"READ_MORE"								=> "更多"
		,"GO_TO_SURVEY"								=> "Go to Survey"
		,"ENHANCE_WELLBEING"						=> "如何提升我的幸福感？"
		,"BRIEF_WELL" 								=> " Stanford幸福感问卷（简版）"
		,"STANFORD_WELL" 							=> " Stanford幸福感问卷"
		,"CERTIFICATES" 							=> "证书"
		,"WELL_SCORE_NA"							=> "<p>抱歉！我们没有足够的数据来计算您今年的幸福感指数。</p><p>请在下一次完全填写整个问卷。</p>"
		,"BRIEF_SCORE"								=> "您的 %m1% Stanford幸福感指数（简版） <b>%m2%/100</b> "
		,"BRIEF_SORRY_NA" 							=> "<p>抱歉！我们没有足够的数据来计算您今年的幸福感指数。</p><p>请在下一次完全填写整个问卷。</p>"
		,"BREIF_DISCLAIMER" 						=> "<i>*对于幸福感指数（简版），我们只能提供总体幸福感得分，而不是各个领域得分。请务必在明年填写原版的幸福感问卷才能获得完整报告。如果您希望比较您的整体幸福感评分，请使用“报告”选项中的“比较”功能。</i>"
		,"RESOURCE_CREATIVITY" 						=> "Exploration and Creativity"
		,"RESOURCE_LIFESTYLE" 						=> "Lifestyle and Daily Practices"
		,"RESOURCE_SOCIAL" 							=> "Social Connectedness"
		,"RESOURCE_STRESS" 							=> "Stress and Resilience"
		,"RESOURCE_EMOTIONS" 						=> "Experience of Emotions"
		,"RESOURCE_SELF" 							=> "Sense of Self"
		,"RESOURCE_PHYSICAL"						=> "Physical Health"
		,"RESOURCE_PURPOSE" 						=> "Purpose and Meaning"
		,"RESOURCE_FINANCIAL" 						=> "Finances"
		,"RESOURCE_RELIGION" 						=> "Spirituality and Religion"
		,"RESOURCE_NONE"							=> "No Current Resources"
		,"YOUR_MET"									=> "Your physical activity MET-minutes/week score is"
		,"CERT_DL"									=> "点击此处下载您的证书！"
		,"WELL_SCORE_YEAR"							=> "您的 Stanford幸福感指数for %m1% is <b class='wellscore'>%m2%</b>"	
		,"UO1_REMINDER"								=> "请记得完成以下中医体質及睡眠品质问卷"
		,"MYWELLTREE"								=> "My Well Tree"


		,""
	));

	$template_security_questions = array(
			'concert'	=> '你参加的第一场音乐会是？',
			'cartoon'	=> '你小时候最喜欢的卡通是？',
			'reception'	=> '你婚礼招待的地点是？',
			'sib_nick'	=> '你年纪最大的兄弟姐妹的昵称是？',
			'street'	=> '你三年级时住的街道名是？',
			'pet'		=> '你的第一只宠物的名字是？',
			'parents'	=> '你的母亲和父亲在哪个城镇第一次遇见？',
			'grammie'	=> '你的祖母的昵称是？',
			'boss'		=> '你的第一个老板的名字是？',
			'sib_mid'	=> '你年纪最大的兄弟姐妹的名字是？',
			'custom'	=> ''
		);

	$websiteName = "WELL for Life";
	
// {
// 	 "translations" : {
// 		 "cn" : {
// 		 		 "wellbeing_questions" 	      	: "身心健康问题"
// 				,"a_little_bit_about_you" 		: "有些关于您"
// 				,"your_physical_activity" 		: "你的身体活动量"
// 				,"your_sleep_habits" 			: "你的睡眠習慣"
// 				,"your_tobacco_and_alcohol_use" : "你的烟草和酒类使用量"
// 				,"your_diet" 					: "你的饮食状况"
// 				,"your_health" 					: "你的健康状况"
// 				,"about_you" 					: "关于您"
// 				,"your_social_and_neighborhood_environment" : "您的社交和邻里环境"
// 				,"contact_information" 			: "联系资料"
// 				,"your_feedback" 				: "您的建议"
// 		}
// 	}
// }
?>

