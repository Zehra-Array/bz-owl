<?php
	class matchServicesRSSSystem
	{
		function convert_datetime($str) {
			list($date, $time) = explode(' ', $str);
			list($year, $month, $day) = explode('-', $date);
			list($hour, $minute, $second) = explode(':', $time);
			
			$ts = mktime($hour, $minute, $second, $month, $day, $year);
			return $ts;
		}
		
		function __construct()
		{
			global $config;
			global $db;
			
			$baseaddress = $config->getValue('baseaddress');
			
			$query = $db->SQL('SELECT timestamp FROM matches ORDER BY timestamp DESC LIMIT 1');
			$row = $db->fetchRow($query);
			$db->free($query);
			
			$numRows = 0;
			if (!$row)
			{
				header('Content-Type: text/plain');
				echo 'No matches played yet.';
				return;
			}
			
			$pubDate = date("Y-m-d\TH:i:s+00:00", $this->convert_datetime($row['timestamp']));
			
			$query = 'SELECT timestamp,team1_points,team2_points,t1.name team1_name,t2.name team2_name ';
			$query .= 'FROM matches LEFT JOIN teams t1 ON matches.team1ID = t1.id LEFT JOIN teams t2 ON matches.team2ID = t2.id ';
			$query .= 'ORDER BY timestamp DESC LIMIT 5';
			
			$numRowsQuery = $db->SQL(str_replace('SELECT ', 'SELECT COUNT(*) AS `numRows`, ', $query));
			$numRowsQuery = $db->fetchRow($numRowsQuery);
			$numRows = $numRowsQuery['numRows'];
			
			$query = $db->SQL($query);
			
			if ($numRows >= 1)
			{
				header('Content-Type: text/xml');
				echo ('<?xml version="1.0" encoding="UTF-8"?>'
					  . '<rss version="2.0"'
					  . 'xmlns:dc="http://purl.org/dc/elements/1.1/"'
					  . 'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"'
					  . 'xmlns:bzl="'.$baseaddress.'">'
					  . '<channel>'
					  . '<title>GU League - Last 5 Match Results</title>'
					  . '<link>'.$baseaddress.'Matches/</link>'
					  . '<description>Herein lies the last 5 match results entered on the BZFlag GU League site.</description>'
					  . '<language>en</language>'
					  . '<copyright>GPL</copyright>'
					  . '<lastBuildDate>'.$pubDate.'</lastBuildDate>'
					  . '<generator>'.$baseaddress.'rss/</generator>'
					  . '<webMaster>'.$baseaddress.'Contact/</webMaster>'
					  . '<ttl>60</ttl>'
					  . '<dc:language>en</dc:language>'
					  . '<dc:creator>'.$baseaddress.'</dc:creator>'
					  . '<dc:rights>GPL</dc:rights>'
					  . '<dc:date>'.$pubDate.'</dc:date>'
					  . '<sy:updatePeriod>hourly</sy:updatePeriod>'
					  . '<sy:updateFrequency>60</sy:updateFrequency>'
					  . '<sy:updateBase>2003-09-01T12:00+00:00</sy:updateBase>');
				while ($row = $db->fetchRow($query))
				{
					$ts = convert_datetime($row['timestamp']);
					$date1 = date('M d H:i', $ts);
					$date2 = date('Y-m-d\TH:i:s+00:00', $ts);
					echo "<item>
					<title>$date1 => {$row['team1_name']}:{$row['team1_points']}, {$row['team2_name']}:{$row['team2_points']}</title>
					<link>$baseaddressMatches/</link>
					<description>$date1 => {$row['team1_name']}:{$row['team1_points']}, {$row['team2_name']}:{$row['team2_points']}</description>
					<pubDate>$date2</pubDate>
					</item>";
				}
				echo '</channel></rss>';
			}
			else {
				header('Content-Type: text/plain');
				echo 'Error: No matches found.';
			}
		}
	}
?>
