<?php
namespace App\Helpers;

use Kreait\Firebase\Factory;

use Kreait\Firebase\ServiceAccount;

use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseHelper {
	public function __construct() {
		$serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/../../black-book-d1b39-firebase-adminsdk-52boi-0a10c155e1.json');

		$this->firebase = (new Factory)
							->withServiceAccount($serviceAccount)
							->create();
	}

	public function sendNotification($topic, $title, $body) {
		$messaging = $this->firebase->getMessaging();

		$message = CloudMessage::fromArray([
			'topic' => $topic,
			'notification' => [
				'title' => $title,
				'body' => $body
			]
		]);

		$messaging->send($message);
	}
}