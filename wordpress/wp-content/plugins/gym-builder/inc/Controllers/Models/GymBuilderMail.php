<?php

namespace GymBuilder\Inc\Controllers\Models;

class GymBuilderMail {
	public static function send_mail( $file_send=false,$args = [] ) {
		$body      = wpautop( html_entity_decode( $args['mail_body'] ) );
		$from_name = html_entity_decode( $args['from_name'] );
		$headers   = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $from_name . ' <' . $args['from'] . '>',
		];

		if ($file_send){
			$file_tmp = $args['file']['mail_file']['tmp_name'];
			$file_name = $args['file']['mail_file']['name'];
			$file_path = wp_upload_dir()['path'] . '/' . $file_name;
			move_uploaded_file($file_tmp, $file_path);
			$attachments = array($file_path);
			$mail_sent = wp_mail($args['to'], $args['subject'], $body, $headers, $attachments);
			if ($mail_sent){
				unlink($file_path);
				return true;
			}
		}else{
			$mail_sent = wp_mail( $args['to'], $args['subject'], $body, $headers );
			if ($mail_sent){
				return true;
			}
		}
		return false;

	}
}