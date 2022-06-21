<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale = 1.0, user-scalable = no">
        <meta http-equiv="cleartype" content="on">

        <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
		<script>
			$(document).ready(function(){
				const knownYears = Array(...new Set([...$('.post-head span').map((e, o) => $(o).text().split(/[,\s]+/).slice(-1)).toArray().sort()])).join(', ')
				console.debug('knownYears', knownYears)
				$('.known-years').text(knownYears)

				let fragReg = undefined;
				let yearReg = undefined;

				function andSearchPosts () {
					$(".post").hide();
					$(".post").each(function(i, obj) {
						const body = $(obj).text()
						const date = $(obj).find('.post-head span').text()
						const foundFrag = fragReg ? fragReg.test(body) : true;
						const foundYear = yearReg ? yearReg.test(date) : true;
						if (foundFrag && foundYear) {
							$(obj).show();
						} else {
							$(obj).hide();
						}
					})
				}

				$("#search").on("input", function() {
					const frag = $("#search").val()
					fragReg = frag ? new RegExp(frag, 'i') : undefined
					andSearchPosts()
				})

				$("#year").on("input", function() {
					const year = $("#year").val()
					yearReg = year ? new RegExp(year, 'i') : undefined
					andSearchPosts()
				})
			});
		</script>

    </head>
    <body>

		<div class="content">

<?php

		$url = "https://tateevents.herokuapp.com/feed.xml";

		$invalidurl = false;
		if(@simplexml_load_file($url)){
			$feeds = simplexml_load_file($url);
		} else {
			$invalidurl = true;
			echo "<h2>Invalid RSS feed URL.</h2>";
		}

		$i=0;
		if(!empty($feeds)) {

			$site = $feeds->channel->title;
?>
			<h2>
				<form>
					<span>Search <?php echo $site; ?></span>
					</br>
					<label for="search">for Text:</label>
					<input type="text" id="search" size="25" autocomplete="on"/>
					</br>
					<label for="year">&nbsp; in Year:</label>
					<input type="text" id="year" size="25" autocomplete="on"/>
				</form>
			</h2>
			(Within: <span class="known-years"></span>)
<?php
			foreach ($feeds->channel->item as $item) {

				$title = $item->title;
				$link = (string) $item->enclosure->attributes()->url;
				$description = $item->description;
				$postDate = $item->pubDate;
				$pubDate = date('D, d M Y',strtotime($postDate));
?>
				<div class="post">
					<div class="post-head">
						<h2><a class="feed_title" href="<?php echo $link; ?>"><?php echo $title; ?></a></h2>
						<span><?php echo $pubDate ?></span>
					</div>
					<div class="post-content">
						<?php echo $description; ?>
					</div>
				</div>
<?php
				$i++;
			}
		} else {
			if(!$invalidurl){
				echo "<h2>No item found</h2>";
			}
		}
?>
    </body>
</html>