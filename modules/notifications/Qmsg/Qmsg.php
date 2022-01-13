<?php

namespace WHMCS\Module\Notification\Qmsg;


use WHMCS\Config\Setting;
use WHMCS\Exception; 
use WHMCS\Module\Notification\DescriptionTrait;
use WHMCS\Module\Contracts\NotificationModuleInterface;
use WHMCS\Notification\Contracts\NotificationInterface;


class Qmsg implements NotificationModuleInterface
{
	use DescriptionTrait;
	
    public function __construct()
    {
        $this->setDisplayName('Qmsg')
            ->setLogoFileName('logo.png');
    }


	public function settings()
    {
        return [
            'botqapi' => [
                'FriendlyName' => 'Api链接',
                'Type' => 'text',
                'Description' => '注意如果有端口记得下端口一般是5700，结尾不要带/',
                'Placeholder' => ' ',
            ],
            'botChatID' => [
                'FriendlyName' => '通知QQ号码或群号',
                'Type' => 'text',
                'Description' => '填写你要推送收到消息的QQ号码',
                'Placeholder' => ' ',
            ],

        ];
    }

	
	public function testConnection($settings)
    {
		$botqapi = $settings['botqapi'];
		$botChatID = $settings['botChatID'];
		
		$message = urlencode("WHMCS通知推送模块启动成功！");
		$response = file_get_contents($botqapi."/send_private_msg?user_id=".$botChatID."&message=".$message);

        if (!$response) { 
			throw new Exception('API没有收到响应');
		}
    }

	public function notificationSettings()
	{
		return [];
	}
	
	public function getDynamicField($fieldName, $settings)
	{
		return [];
	}


	public function sendNotification(NotificationInterface $notification, $moduleSettings, $notificationSettings)
    {
        $botqapi = $moduleSettings['botqapi'];
		$botChatID = $moduleSettings['botChatID'];
		
		$messageContent = "【". $notification->getTitle() ."】\n\n通知事项：". $notification->getMessage() ."\n\n快捷链接：". $notification->getUrl() ."";
		
		$message = urlencode($messageContent);
		$response = file_get_contents($botqapi."/send_private_msg?user_id=".$botChatID."&message=".$message);
		
        if (!$response) { 
			throw new Exception('API没有收到响应');
		}
    }
}
