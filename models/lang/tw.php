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
		"ACCOUNT_SPECIFY_F_L_NAME" 				=> "請輸入您的名字和姓氏",
		"ACCOUNT_SPECIFY_USERNAME" 				=> "請輸入您的用戶帳號",
		"ACCOUNT_SPECIFY_PASSWORD" 				=> "請輸入您的密碼",
		"ACCOUNT_SPECIFY_EMAIL"					=> "請輸入您的電子郵件地址。",
		"ACCOUNT_INVALID_EMAIL"					=> "無效的電子郵件地址。",
		"ACCOUNT_EMAIL_MISMATCH"				=> "電子郵件必須符合。",
		"ACCOUNT_USER_OR_PASS_INVALID"			=> "電子郵件和/或密碼不被識別。",
		"ACCOUNT_PASS_MISMATCH"					=> "密碼必須符合。",
		"ACCOUNT_EMAIL_IN_USE_ACTIVE"			=> "電子郵件 %m1% 已在使用中。如果您忘記了密碼，您可以重新設定於 <a href='login.php'>登錄表單</a>",
		"ACCOUNT_NEW_ACTIVATION_SENT"			=> "感謝您註冊WELL for Life計劃！我們已向您的電子郵件發送了帳戶激活鏈接。請檢查您的電子郵件，並點擊裡面的鏈接。如果您在1小時內沒有收到電子郵件，請通過wellforlife@stanford.edu與我們聯繫。",
		"ACCOUNT_SPECIFY_NEW_PASSWORD"			=> "請輸入您的新密碼。",	
		"ACCOUNT_NOT_YET_ELIGIBLE"				=> "感謝您對WELL for Life計劃的關注！您目前沒有資格參加。%m1% 當計劃擴大時，我們將與您聯繫關於WELL for Life相關研究和信息。",
		"ACCOUNT_NEED_LOCATION"					=> "請輸入您的郵政編碼或城市。",
		"ACCOUNT_TOO_YOUNG"						=> "您尚未年滿18歲。",
		"ACCOUNT_NOT_IN_USA"					=> "本研究計劃僅適用於居住在美國的參與者。",
		"ACCOUNT_NOT_IN_ELIGIBLE_LOCATION"		=> "本研究計劃僅適用於居住在美國的參與者。",
		"ACTIVATION_MESSAGE"					=> "您需要首先激活您的帳戶，然後才能登錄。請按照以下鏈接激活您的帳戶。 \n\n%m1%register.php?activation=%m2%",							
		"ACCOUNT_ERROR_TRY_AGAIN"				=> "再試一次...", 
		"ACCOUNT_ERROR_ATTEMPTS"				=> " 剩餘嘗試次數。",
		"ACCOUNT_ERROR_ATTEMPT"					=> " 剩餘嘗試次數。", 

		//REGISTER
		"ACCOUNT_REGISTER" 						=> "註冊本研究計劃",
		"ACCOUNT_YOUR_NAME"						=> "您的姓名",
		"ACCOUNT_FIRST_NAME" 					=> "英文名字",
		"ACCOUNT_LAST_NAME" 					=> "英文姓氏",
		"ACCOUNT_YOUR_EMAIL" 					=> "電子郵件",
		"ACCOUNT_EMAIL_ADDRESS" 				=> "電子郵件地址",
		"ACCOUNT_EMAIL_ADDRESS_OR_USERNAME"		=> "電子郵件或用戶帳號",
		"ACCOUNT_USERNAME"						=> "用戶帳號",
		"ACCOUNT_PARTICIPANT_ID"				=> "參與者編號",
		"ACCOUNT_REENTER_EMAIL" 				=> "重新輸入電子郵件",
		"ACCOUNT_YOUR_LOCATION" 				=> "您居住的地區",
		"ACCOUNT_CITY" 							=> "英文城市",
		"ACCOUNT_ZIP" 							=> "郵政編碼",
		"ACCOUNT_ALREADY_REGISTERED" 			=> "已經註冊過？",
		"ACCOUNT_BIRTH_YEAR" 					=> "你的出生年份是?",
		"ACCOUNT_18_PLUS" 						=> "你滿18歲嗎？",
		"ACCOUNT_USA_CURRENT" 					=> "你現在住在美國嗎？",
		"ACCOUNT_AGREE" 						=> "點擊送出按鈕,我同意被聯繫有關WELL for Life相關研究和信息。",
		"ACCOUNT_ELITE_THANKS" 					=> "感謝您成為我們首批500名參與者之一。我們收集的數據將幫助我們改善我們的健康！",
		"STEP_REGISTER"							=> "註冊",
		"STEP_VERIFY"							=> "驗證郵件",
		"STEP_CONSENT"							=> "同意",
		"STEP_SECURITY"							=> "安全性",

		"ACCOUNT_NEW_PASSWORD" 					=> "新密碼",
		"ACCOUNT_PASSWORD" 						=> "密碼",
		"ACCOUNT_PASSWORD_AGAIN" 				=> "密碼再一次",

		"ACCOUNT_LOGIN_PAGE" 					=> "登入頁面",
		"ACCOUNT_REGISTER_PAGE" 				=> "註冊頁面",
		
		"REGISTER_STUDY" 						=> "註冊本研究計劃",
		"REGISTER_TOKEN_INVALID_1" 				=> "提供的電子郵件激活鏈接無效或已過期。",
		"REGISTER_TOKEN_INVALID_2" 				=> "電子郵件激活鏈接無效 <br><a class='alink' href='login.php'>點擊此處</a> 並選擇“忘記密碼”獲取新的鏈接。",

		//LOGIN
		"ACCOUNT_LOGIN_CONTINUE" 				=> "請登入完後再繼續",
		"ACCOUNT_LOGIN_NOW" 					=> "登入",
		"ACCOUNT_NEXT_STEP" 					=> "下一步",
		
		//CONSENT
		"IRB_ONLY" 								=> "IRB Use Only",
		"IRB_EXPIRATION"						=> "Expiration Date",
		"CONSENT_BULLET_1" 						=> "我們需要您的許可，我們才能向您提出任何問題，因此請閱讀以下同意文件",
		"CONSENT_BULLET_2" 						=> "初步問卷需時30分鐘完成。但你不需要一次性填寫完。",
		"CONSENT_BULLET_3" 						=> "每隔幾個月我們會联系您有關WELL的最新資訊。",
		"CONSENT_BULLET_4" 						=> "我們將會持續加入新的問卷，材料和內容，並邀請您參與。",
		"CONSENT_WELCOME" 						=> "歡迎加入!",
		"CONSENT_CONTACT" 						=> "如您對本研究有任何問題，請聯繫總監 Dr. Ann Hsing at annhsing@stanford.edu。",
		"CONSENT_I_AGREE" 						=> "我同意",
		"CONSENT_PRINT" 						=> "列印",
		
		//FORGOT PASSWORD AND ACCOUNT SETUP
		"FORGOTPASS" 							=> "忘記密碼？",
		"FORGOTPASS_RESET" 						=> "重設密碼",
		"FORGOTPASS_RESET_FORM" 				=> "密碼重新設定表單",
		"FORGOTPASS_PLEASE_ANSWER" 				=> "請回答您設定的安全問題。",
		"FORGOTPASS_RECOVERY_ANSWER" 			=> "恢復原密碼的答案。",
		"FORGOTPASS_SEC_Q" 						=> "安全性問題。",
		"FORGOTPASS_ANSWER_QS" 					=> "回答設訂的安全性問題。",
		"FORGOTPASS_EMAIL_ME" 					=> "請送我密碼重新設定的電子郵件。",
		"FORGOTPASS_RECOVERY_METHOD" 			=> "選擇回復方法",
		"FORGOTPASS_BEGIN_RESET" 				=> "輸入電子郵件以開始密碼重新設置",
		"FORGOTPASS_SUGGEST"					=> "點擊 “忘記密碼？” 來重新設置密碼。或<a href=\"register.php\">註冊加入</a>.",
		"FORGOTPASS_INVALID_TOKEN"				=> "無效鏈接",
		"FORGOTPASS_REQUEST_EXISTS"				=> "忘記的密碼授權電子郵件已於 %m1% 分鐘前發送。<br>請檢查您的電子郵件或稍後再試。",
		"FORGOTPASS_REQUEST_SUCCESS"			=> "已啟動密碼重設程序。<br>請查看您的電子郵件以了解後續步驟。",
		"FORGOTPASS_CREATED" 					=> "Password Created",
		"FORGOTPASS_UPDATED" 					=> "密碼更新",
		"FORGOTPASS_INVALID_VALUE" 				=> "密碼重置值無效",
		"FORGOTPASS_Q_UPDATED" 					=> "恢復密碼問題更新！",
		"FORGOTPASS_SEC_Q_SETUP" 				=> "密碼設置和安全性問題",
		"FORGOTPASS_SEC_Q_ANSWERS" 				=> "為了幫助您恢復丟失或忘記的密碼，請提供以下安全問題的答案。",
		"FORGOTPASS_CHOSE_QUESTION" 			=> "請從列表中選擇一個問題",
		"FORGOTPASS_WRITE_CUSTOM_Q" 			=> "編寫自定安全性問題",

		//MAIL
		"MAIL_ERROR"							=> "嘗試郵件時出錯，請與網站管理員聯繫",
		"MAIL_TEMPLATE_BUILD_ERROR"				=> "建構電子郵件範本時出錯",
		"MAIL_TEMPLATE_DIRECTORY_ERROR"			=> "無法打開郵件範本目錄。也許嘗試設置郵件目錄於 %m1%",
		"MAIL_TEMPLATE_FILE_EMPTY"				=> "範本文件是空的...沒有發送任何文件",
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
		 "WELL_FOR_LIFE" 							=> "幸福人生"
		,"MY_DASHBOARD" 							=> "信息中心"
		,"CORE_SURVEYS" 							=> "我的問卷"
		,"COMPLETED_SURVEYS" 						=> "已完成的問卷"
		,"LOGOUT" 									=> "登出"
		,"MY_WELL"									=> "我的幸福感"
		,"MY_STUDIES"								=> "參與研究"
		,"MY_REPORTS"								=> "我的報告"
		,"MY_ASSESSMENTS" 							=> "我的報告"
		,"NO_ASSESSMENTS"							=> "請先完成附加問卷，即可查看您的評估報告"
		,"YOUR_ASSESSMENT"							=> "您的評估報告"
        ,"MY_PROFILE" 								=> "個人資料"
		,"CONTACT_US" 								=> "聯繫我們"
		,"GET_HELP" 								=> "需要幫助"
	     ,"GET_HELP_TEXT" 						   => "<p>對於醫療緊急情況，請致電911或您的醫療保健提供者。</p><p>對於精神健康問題，請參考 <a href=\"https://www.mentalhealth.gov/get-help/\" class='offsite'>MentalHealth.gov</a>.</p>"
		,"QUESTION_FOR_WELL" 						=> "對我們有疑問"
		,"YOUVE_BEEN_AWARDED" 						=> "你已经获得了"
		,"GET_WHOLE_BASKET" 						=> "獲得整籃水果！"
		,"CONTINUE_SURVEY" 							=> "繼續完成剩下的問卷。"
		,"CONGRATS_FRUITS" 							=> "恭喜，你得到了所有的水果！<br/><br>"
		,"CONGRATS_CERT" 							=> "Before we present you with your WELL score<br/> and completion certifcate.<br/> Please confirm your email address.<br><br>"
    	,"THANKS" 							        => "Thank you.<br/> For a more in-depth report of your WELL score,<br> visit the 'Reports' tab."
    	,"FITNESS_BADGE" 							=> "您已獲得健身徽章"
		,"GET_ALL_BADGES" 							=> "獲得所有的健身徽章！"
		,"CONGRATS_ALL_FITNESS_BADGES"				=> "恭喜，你已獲得所有的健身徽章！ <br/>請稍後再回來取得新的獎勵！"
		,"DONE_CORE" 								=> "完成所有主要問卷！"
		,"TAKE_BLOCK_DIET" 							=> "所有WELL參與者可免費參與飲食評估。此問卷通常需要30-50分鐘完成，並提供即時評估報告。"
		,"HOW_WELL_EAT" 							=> "您吃得健康嗎？"
		,"COMPLETE_CORE_FIRST" 						=> "請先完成主要問卷"
		,"PLEASE_COMPLETE" 							=> "請完成 "
		,"WELCOME_TO_WELL" 							=> "<b>歡迎</b>參與WELL人生研究計劃！<u>點擊此處</u>開始您的WELL旅程！</a>"
		,"WELCOME_BACK_TO" 							=> "<b>歡迎回到</b>WELL人生！</a>"
		,"REMINDERS" 								=> " 
温馨提醒"
		,"ADDITIONAL_SURVEYS" 						=> "附加問卷"
		,"SEE_PA_DATA" 								=> "填寫問卷的 “您的身體活動” 部分，可看到您與其他參與者的數據比較圖表！"
		,"HOW_DO_YOU_COMPARE" 						=> "您與其他參與者比較圖表"
		,"SHORT_SCORE_OVER_TIME"					=> "您的幸福感指數"
		,"OTHERS_WELL_SCORES"						=> "您過去幾年的幸福感指數"
		,"OTHERS_SCORE"								=> "參與者平均分數"
		,"USERS_SCORE"								=> "您的分數"
		,"HIGHER_WELLBEING"							=> "高的幸福感指數"
		,"LOWER_WELLBEING"							=> "低的幸福感指數"
		,"NOT_ENOUGH_USER_DATA" 					=> "請完成問卷才能計算您的幸福感指數。"
		,"NOT_ENOUGH_OTHER_DATA" 					=> "沒有足夠的數據來計算平均值。"
		,"SITTING" 									=> "坐著" 
		,"WALKING" 									=> "走路"
		,"MODACT" 									=> "中度身體活動"
		,"VIGACT" 									=> "重度身體活动"
		,"NOACT" 									=> "輕度/無活動"
		,"SLEEP" 									=> "睡眠"
		,"YOU_HOURS_DAY"						             => "您（小時/天）"
		,"AVG_ALL_USERS" 							=> "平均所有參與者（小時/天）"
		,"HOW_YOU_SPEND_TIME" 						=> "您每天如何分配時間"
		,"SUNRISE" 									=> "日出"
		,"SUNSET" 									=> "日落"
		,"WIND" 									=> "有風"
		,"DASHBOARD"								=> "信息中心"
		,"WELCOME_BACK"								=> "歡迎回來"
		,"SUBMIT"									=> "送出"
		,"SAVE_EXIT"								=> "儲存並退出"
		,"SUBMIT_NEXT"								=> "下一步"
		,"MAT_DATA_DISCLAIM" 						=> "以下數據部分是參考心肺健康和國家健康標準的研究而準備的。這些結果不能替代醫療保健提供者的建議。在做出任何可能影響您健康的改變之前，請諮詢您的醫生。"
		,"MAT_SCORE_40"								=> "在接下來的4年中，和你的分數相同的人（10分中有6.6分）可能失去做他們喜歡或珍惜的活動的能力。然而，有很多方法你可以做，以提高你的功能性能力。"
		,"MAT_SCORE_50"								=> "在接下來的4年中，和你的分數相同的人（10分中有5.2分）可能失去做他們喜歡或珍惜的活動的能力。然而，有很多方法你可以做，以提高你的功能性能力。"
		,"MAT_SCORE_60"								=> "在接下來的4年中，和你的分數相同的人（10分中有3.5分）可能失去做他們喜歡或珍惜的活動的能力。然而，有很多方法你可以做，以提高你的功能性能力。."
		,"MAT_SCORE_70"								=> "在接下來的4年中，和你的分數相同的人應該不會失去做他們喜歡或珍惜的活動的能力。然而，有很多方法你可以做，以提高你的功能性能力。繼續您的努力，並嘗試保持您的功能性能力！"
		,"TCM_POSITIVE"								=> "肯定"
		,"TCM_NEGATIVE"								=> "没有"
		,"TCM_ESSENTIALLY_POS"						=> "傾向肯定"

		,"PROFILE_JOINED"							=> "已加入"
		,"PROFILE_NICKNAME"							=> "暱稱"
		,"ACCOUNT_MIDDLE_NAME"						=> "中間名字"
		,"PROFILE_CONTACT_NAME"						=> "聯絡人姓名"
		,"PROFILE_CONTACT_PHONE"					=> "聯絡電話"
		,"PROFILE_STREET_ADDRESS"					=> "街道地址"
		,"PROFILE_APARTMENT"						=> "公寓號碼"
		,"ACCOUNT_STATE"							=> "州"
		,"EDIT_PROFILE"								=> "編輯"
		,"PROFILE_EDIT"								=> "個人資料"

		,"NAV_HOME" 								=> "首頁"
		,"NAV_RESOURCES"							=> "資源"
		,"NAV_REWARDS"								=> "Rewards"
		,"NAV_REPORTS"								=> "報告"
		,"NAV_GAME" 								=> "遊戲"
		,"NAV_ACTIVITY" 							=> "活動"
    	,"NAV_CHALLENGES" 							=> "WELL Challenges"

	    ,"DOMAIN_RANKING" 							=> "Rank your domains"
		,"DOMAIN_RANKING_PROMPT" 					=> "You’re half way done! Before continuing, Please take a moment to complete the Domains of Well-Being Activity... Redirecting in 3 ... 2 ... 1 ..."
		,"YOUR_DOMAIN_RANKING" 						=> "Your well-being domain rankings"
		,"READ_MORE"								=> "更多"
		,"GO_TO_SURVEY"								=> "Go to Survey"
		,"ENHANCE_WELLBEING"						=> "如何提升我的幸福感？"
		,"BRIEF_WELL" 								=> "Stanford幸福感問卷（簡版）"
		,"STANFORD_WELL" 							=> "Stanford幸福感問卷"
		,"CERTIFICATES" 							=> "證書"
		,"WELL_SCORE_NA"							=> "<p>抱歉！我們沒有足夠的數據來計算您今年的幸福感指數。</p><p>請在下一次完全填寫整個問卷。</p>"
		,"BRIEF_SCORE"								=> "您的 %m1% Stanford幸福感指數（簡版） <b>%m2%/100</b> "
		,"BRIEF_SORRY_NA" 							=> "<p>抱歉！我們沒有足夠的數據來計算您今年的幸福感指數。</p><p>請在下一次完全填寫整個問卷。</p>"
		,"BREIF_DISCLAIMER" 						=> "<i>*對於幸福感指數（簡版），我們只能提供總體幸福感得分，而不是各個領域得分。請務必在明年填寫原版的幸福感問卷才能獲得完整報告。如果您希望比較您的整體幸福感評分，請使用“報告”選項中的“比較”功能。</i>"
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
		,"CERT_DL"									=> "點擊此處下載您的證書！"
		,"WELL_SCORE_YEAR"							=> "您的 Stanford幸福感指數for %m1% is <b class='wellscore'>%m2%</b>"	
		,"UO1_REMINDER"								=> "請記得完成以下中醫體質及睡眠品質問卷"
		,"MYWELLTREE"								=> "My Well Tree"
	));

	$template_security_questions = array(
			'concert'	=> '你參加的第一場音樂會是？',
			'cartoon'	=> '你小時候最喜歡的卡通是？',
			'reception'	=> '你婚禮招待的地點是？',
			'sib_nick'	=> '你年紀最大的兄弟姐妹的暱稱是？',
			'street'	=> '你三年級時住的街道名是？',
			'pet'		=> '你的第一隻寵物的名字是？',
			'parents'	=> '你的母親和父親在哪個城鎮第一次遇見？',
			'grammie'	=> '你的祖母的暱稱是？',
			'boss'		=> '你的第一個老闆的名字是？',
			'sib_mid'	=> '你年紀最大的兄弟姐妹的名字是？',
			'custom'	=> ''
		);

	$websiteName = "WELL for Life";



// {
// 	 "translations" : {
// 		 "tw" : {
// 		 		 "wellbeing_questions" 	      	: "身心健康問題"
// 				,"a_little_bit_about_you" 		: "有些關於您"
// 				,"your_physical_activity" 		: "你的身體活動量"
// 				,"your_sleep_habits" 			: "你的睡眠習慣"
// 				,"your_tobacco_and_alcohol_use" : "你的煙草和酒類使用量"
// 				,"your_diet" 					: "你的飲食狀況"
// 				,"your_health" 					: "你的健康狀況"
// 				,"about_you" 					: "關於您"
// 				,"your_social_and_neighborhood_environment" : "您的社交和鄰里環境"
// 				,"contact_information" 			: "聯繫資料"
// 				,"your_feedback" 				: "您的建議"
// 		}
// 	}
// }
?>



