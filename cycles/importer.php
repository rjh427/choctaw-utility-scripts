<?php
	require_once 'simple_html_dom.php';
	require_once 'arrays.php';

	static $postId = 1;
	static $commentId = 100;
	$usersList = true;

	foreach ($pages as $page) {
		$page = $pages[16];
		$postId++;
		$string = '';
		// 0 = no comments
		// 1 = simple comments
		// 2 = complex comments
		$html = new simple_html_dom();
		$html->load_file($page[0]);

		$cats = [
			'old' => ['bicycles','blog','construction','court','cycling education','Easthampton','Hadley police','League of American Bicyclists','miscellaneous','saga','West Springfield police'],
			'new' => [], // imported categories end up here
		];

		foreach ($html->find('.sf_blog_postmeta a') as $element) {
			if (in_array($element->plaintext, $cats['old'])) {
				$cats['new'][] = $element->plaintext;
			}
		}

		$title = trim(substr(strip_tags($html->find('.sf_pagetitle', 0)), 7));
		$content = $html->find('.sf_blog_entry', 0);

//		$creator = substr(trim(strip_tags(substr($creator = $html->find('.sf_blog_postmeta', 0), 43, strpos($creator = $html->find('.sf_blog_postmeta', 0), ' at ')))),0, -14);

		$creator = explode(' ', trim(strip_tags($html->find('.sf_blog_postmeta', 0))));
		$postDate = strtotime(substr($html->find('.sf_blog_postmeta a')[0]->plaintext, 0, -3));

		//echo 'url: ', $page[0], '<br>';
		// var_dump($pages[15][1]); // check digit
		echo $page[0];
		#echo '$title: ', $title, '<br>';
		#echo '$creator: ', $creator, '<br>';
		#echo '$pubDate: ', $pubDate, '<br>';
		#echo '$postDate: ', $postDate, '<br>';

		if ($usersList == false) {
			$string .= getUsers($users);
			$usersList = false;
		}
		
		$string .= $page[0] . "\n";
		$string .= '    <item>' . "\n";
		$string .= '      <title>' . $title . '</title>' . "\n";
		$string .= '      <dc:creator><![CDATA[' . $creator[2] . ']]></dc:creator>' . "\n";
		$string .= '      <description>' . $title . '</description>' . "\n";
		$string .= '      <wp:post_id>' . $postId . '</wp:post_id>' . "\n";
		$string .= '      <content:encoded><![CDATA[' . $content . ']]></content:encoded>' . "\n";
		$string .= '      <excerpt:encoded><![CDATA[]]></excerpt:encoded>' . "\n";
		$string .= '      <wp:is_sticky>0</wp:is_sticky>' . "\n";
		$string .= '      <wp:post_date>' . date('Y-m-d H:i:s', $postDate) . '</wp:post_date>' . "\n";
		$string .= '      <wp:comment_status><![CDATA[closed]]></wp:comment_status>' . "\n";
		$string .= '      <wp:ping_status><![CDATA[closed]]></wp:ping_status>' . "\n";
		$string .= '      <wp:post_name><![CDATA[' . $title . ']]></wp:post_name>' . "\n";
		$string .= '      <wp:status>publish</wp:status>' . "\n";
		if (count($cats['new']) > 0) {
			$string .= '      <wp:post_type>post</wp:post_type>' . "\n";
			foreach ($cats['new'] as $cat) {
				$string .= '      <category domain="category" nicename="' . $cat . '"><![CDATA[' . ucfirst($cat) . ']]></category>' . "\n";
			}
			$string .= '      <wp:postmeta>' . "\n";
		}
		$string .= '        <wp:meta_key><![CDATA[_wp_old_slug]]></wp:meta_key>' . "\n";
		$string .= '        <wp:meta_value><![CDATA[]]></wp:meta_value>' . "\n";
		$string .= '      </wp:postmeta>' . "\n";
		$string .= '      <wp:postmeta>' . "\n";
		$string .= '        <wp:meta_key><![CDATA[seo_description]]></wp:meta_key>' . "\n";
		$string .= '        <wp:meta_value><![CDATA[]]></wp:meta_value>' . "\n";
		$string .= '      </wp:postmeta>' . "\n";

		switch ($page[1]) {
			case '0':
				continue;
				break;
			case '1':
			case '2':
				$string .= getSimpleComments($html->find('.commentlist li'));
				break;
			case '3':
				$replies = getComments($html->find('.commentlist li'));
				// $replyDate, $replyAuthor, $replyText, $replyUrl
				$string .= '      <wp:comment>' . "\n";
				$string .= '        <wp:comment_id>' . $commentId++ . '</wp:comment_id>' . "\n";
				$string .= '        <wp:comment_author><![CDATA[' . $replies[1] . ']]></wp:comment_author>' . "\n";
				$string .= '        <wp:comment_author_email><![CDATA[' . strtolower(str_replace(' ','.',$replies[1])) . '@fixme.org]]></wp:comment_author_email>' . "\n";
				$string .= '        <wp:comment_author_url>' . $replies[3] . '</wp:comment_author_url>' . "\n";
				$string .= '        <wp:comment_date><![CDATA[' . $replies[0] . ']]></wp:comment_date>' . "\n";
				$string .= '        <wp:comment_content><![CDATA[' . $replies[2] . ']]></wp:comment_content>' . "\n";
				$string .= '        <wp:comment_approved><![CDATA[1]]></wp:comment_approved>' . "\n";
				$string .= '        <wp:comment_type><![CDATA[Comment]]></wp:comment_type>' . "\n";
				$string .= '        <wp:comment_parent>' . $postId . '</wp:comment_parent>' . "\n";
				$string .= '        <wp:comment_user_id> 	</wp:comment_user_id>' . "\n";
				$string .= '      </wp:comment>' . "\n";
		}

		$string .= '    </item>' . "\n";
		file_put_contents('fragments/' . $postId . '.xml', $string);
		die;
		if ($postId > 20) {
			die;
		}
	}


	function getSimpleComments($arr) {
		global $html;
		global $page;
		global $postId;
		global $commentId;
		$string = '';
		$replies = [];
		if (count($arr) == null || count($arr) == 0) {
			return null;
		}

		foreach ($arr as $ar) {
			if (isset($ar->find('a', 1)->href)) {
				$replyUrl = $ar->find('a', 1)->href;
			} else {
				$replyUrl = '';
			}

			$commentTimeDateStamp = DateTime::createFromFormat('n/d/Y g:i:s A', trim($ar->find('[!href]',1)));
			$commentDate = $commentTimeDateStamp->format('Y-m-d H:i:s');


			$string .= '      <wp:comment>' . "\n";
			$string .= '        <wp:comment_id>' . $commentId++ . '</wp:comment_id>' . "\n";
			$string .= '        <wp:comment_author><![CDATA[' . $ar->find('a', 1)->plaintext . ']]></wp:comment_author>' . "\n";
			$string .= '        <wp:comment_author_email><![CDATA[' . strtolower(str_replace(' ','.', $ar->find('a', 1)->plaintext)) . '@fixme.org]]></wp:comment_author_email>' . "\n";
			$string .= '        <wp:comment_author_url>' . $replyUrl . '</wp:comment_author_url>' . "\n";
			$string .= '        <wp:comment_date><![CDATA[' . $commentDate . ']]></wp:comment_date>' . "\n";
			$string .= '        <wp:comment_content><![CDATA[' . cleanText($ar) . ']]></wp:comment_content>' . "\n";
			$string .= '        <wp:comment_approved><![CDATA[1]]></wp:comment_approved>' . "\n";
			$string .= '        <wp:comment_type><![CDATA[Comment]]></wp:comment_type>' . "\n";
			$string .= '        <wp:comment_parent>' . $postId . '</wp:comment_parent>' . "\n";
			$string .= '        <wp:comment_user_id> 	</wp:comment_user_id>' . "\n";
			$string .= '      </wp:comment>' . "\n";
		}

		/*
		for ($i=0; $i < count($arr); $i++) {
			$commentTimeDateStamp = DateTime::createFromFormat('n/d/Y g:i:s A', trim($arr[0]->find('[!href]',1)));
			$commentPostDate = $commentTimeDateStamp->format('Y-m-d H:i:s');
			$replyDate = $commentPostDate; // comment timedate
			$replyAuthor = $arr[0]->find('a', 1)->plaintext; // comment author
			$allowedTags = '<p>, <br>, <br />';
			$replyText = strip_tags($arr[0], $allowedTags); // comment
			$replyText = str_replace("\t", '', $replyText);
			$replyText = str_replace('Reply to this', '', $replyText);
			$commentArr = explode(':', $replyText);
			$replyText = $commentArr[3];
			
			if (isset($arr[0]->find('a', 1)->value)) {
				$replyUrl = $arr[0]->find('a', 1)->value;
			} else {
				$replyUrl = '';
			}
			if (retrieveText($replyDate, $replyAuthor, $replyText) == true) {
				$replies[] = trim($replyDate);
				$replies[] = trim($replyAuthor);
				$replies[] = trim($replyText);
				$replies[] = trim($replyUrl);
				return $replies;
			} else {
				return null;
			}
		}
		*/
		$counter = 0;
		echo PHP_EOL, PHP_EOL, 'counter: ', ++$counter, PHP_EOL, 'string: ', $string, PHP_EOL, PHP_EOL;
		return $string;
	}


	function getComments($arr) {
		global $html;
		global $page;
		$replies = [];
		if (count($arr) == null || count($arr) == 0) {
			return null;
		}

		for ($i=0; $i < count($arr); $i++) {
			$commentTimeDateStamp = DateTime::createFromFormat('n/d/Y g:i:s A', trim($arr[0]->find('[!href]',1)));
			$commentPostDate = $commentTimeDateStamp->format('Y-m-d H:i:s');
			$replyDate = $commentPostDate; // comment timedate
			$replyAuthor = $arr[0]->find('a', 1)->plaintext; // comment author
			$allowedTags = '<p>, <br>, <br />';
			$replyText = strip_tags($arr[0], $allowedTags); // comment
			$replyText = str_replace("\t", '', $replyText);
			$replyText = str_replace('Reply to this', '', $replyText);
			$commentArr = explode(':', $replyText);
			$replyText = $commentArr[3];
			
			if (isset($arr[0]->find('a', 1)->value)) {
				$replyUrl = $arr[0]->find('a', 1)->value;
			} else {
				$replyUrl = '';
			}
			if (retrieveText($replyDate, $replyAuthor, $replyText) == true) {
				$replies[] = trim($replyDate);
				$replies[] = trim($replyAuthor);
				$replies[] = trim($replyText);
				$replies[] = trim($replyUrl);
				return $replies;
			} else {
				return null;
			}
		/*
			$comments = $html->find('.commentlist li');
			foreach ($comments as $comment) {
				$commentDate = trim(strip_tags($comment->find('[!href]',1))); // timedate
				$commentAuthor = trim(strip_tags($comment->find('a', 1)->plaintext));
				$commentText = trim(strip_tags($comment->find('[!href]',6))); // comments)
				//	echo $commentDate, '<br>';
				//	echo $commentAuthor, '<br>';
				//	echo $commentText, '<br>';
				//	echo '<hr>';
			}

			//echo '<hr><hr><hr>', PHP_EOL;
			$counter = 0;
			foreach ($comments as $reply) {

			$replyDate = $reply->find('[!href]',1); // timedate
			$replyAuthor = $reply->find('a', 1)->plaintext;

			//	$replyText = $reply->find('[!href]',6); // comments
			$replyText = $html->find('li[id^=comment-]', $counter++); // comments
			$replies = retrieveText($replyDate, $replyAuthor, $replyText);
			echo ($comm);
			//	break;
			echo '<hr>';
		*/

		}
	}

	function retrieveText($str1, $str2=null, $str3=null) {
		trim($str1);
		trim($str2);
		trim($str3);
		if (strcmp($str2, 'Reply to this') == 0) {
			return false;
		}
		return true;
	}

	function getUsers($users) {
		$authors = '';
		foreach ($users as $user) {
			$authors .= '    <wp:wp_author>' . "\n";
			$authors .= '      <wp:author_login>' . $user['name'] . '</wp:author_login>' . "\n";
			$authors .= '      <wp:author_id>' . $user['id'] . '</wp:author_id>' . "\n";
			$authors .= '      <wp:author_email>' . $user['email'] . '</wp:author_email>' . "\n";
			$authors .= '      <wp:author_display_name><![CDATA[' . $user['name'] . ']]></wp:author_display_name>' . "\n";
			$authors .= '      <wp:author_first_name><![CDATA[' . $user['first'] . ']]></wp:author_first_name>' . "\n";
			$authors .= '      <wp:author_last_name><![CDATA[' . $user['last'] . ']]></wp:author_last_name>' . "\n";
			$authors .= '    </wp:wp_author>' . "\n";
		}
		//file_put_contents('fragments/authors.xml', getUsers($users));
		return $authors;
	}

	function cleanText($text) {
		$replyText = '';
		$allowedTags = 
			'<p>, <br>, <br/>, <br />, <h1>, <h2>, <h3>, <h4>, <h5>, <h6>, 
			 <blockquote>, <span>, <strong>, <img>, <ul>, <ol>, <li>, <b>, <i>, 
			 <em>, <iframe>
			';
		$replyText = strip_tags($text, $allowedTags); // comment
		$replyText = str_replace("\t", '', $replyText);
		$replyText = str_replace('....', '', $replyText);
		$replyText = str_replace('..', '', $replyText);
		$replyText = str_replace('Reply to this', '', $replyText);
		$commentArr = explode(':', $replyText);
		$replyText = $commentArr[3];
		return $replyText;
	}

	function getUserId($user) {
		global $users;
		// return user ID #
	}
