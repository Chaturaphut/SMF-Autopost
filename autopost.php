<?php
// Autopost Class 
class Autopost
{
    function Autopost() {
        echo "Putter SMF Auto Post 1.0<br>";
    }
     
    function tis620_to_utf8($tis) 
                    {
                        for( $i=0 ; $i< strlen($tis) ; $i++ )
                            {
                                        $s = substr($tis, $i, 1);
                                         $val = ord($s);
                                     
                                 if( $val < 0x80 )
                                    {
                                        $utf8 .= $s;
                                    } 
                                elseif ((0xA1 <= $val and $val <= 0xDA) or (0xDF <= $val and $val <= 0xFB)) 
                                {
                                    $unicode = 0x0E00 + $val - 0xA0;
                                    $utf8 .= chr( 0xE0 | ($unicode >> 12) );
                                    $utf8 .= chr( 0x80 | (($unicode >> 6) & 0x3F) );
                                    $utf8 .= chr( 0x80 | ($unicode & 0x3F) );
                                }
                            }
                    return $utf8;
                }
             
             
        function utf8_to_tis620($string) 
            {
                 $str = $string;
                 $res = "";
             
                    for ($i = 0; $i < strlen($str); $i++) 
                    {
                        if (ord($str[$i]) == 224) 
                            {
                                $unicode = ord($str[$i+2]) & 0x3F;
                                $unicode |= (ord($str[$i+1]) & 0x3F) << 6;
                                $unicode |= (ord($str[$i]) & 0x0F) << 12;
                                $res .= chr($unicode-0x0E00+0xA0);
                                $i += 2;
                            } 
                        else
                            {
                             $res .= $str[$i];
                            }
                     }
                         return $res;
            }
 
     
    function html2bbc($data)
        {
            $htmltags = array(
                            '/\<b\>(.*?)\<\/b\>/is',
                            '/\<i\>(.*?)\<\/i\>/is',
                            '/\<u\>(.*?)\<\/u\>/is',
                            '/\<ul\>(.*?)\<\/ul\>/is',
                            '/\<li\>(.*?)\<\/li\>/is',
                            '/\<img(.*?) src=\"(.*?)\" (.*?)\>/is',
                            '/\<div(.*?)\>(.*?)\<\/div\>/is',
                            '/\<br(.*?)\>/is',
                            '/\<strong\>(.*?)\<\/strong\>/is',
                            '/\<a href=\"(.*?)\"(.*?)\>(.*?)\<\/a\>/is',
                            '/\<code\>(.*?)\<\/code\>/is',
                            '/\<span style=\"color:(.*?)\"\>(.*?)\<\/span\>/is',
                            '/\<blockquote\>(.*?)\<\/blockquote\>/is',  
                            );
 
            $bbtags = array(
                            '[b]$1[/b]',
                            '[i]$1[/i]',
                            '[u]$1[/u]',
                            '[list]$1[/list]',
                            '[*]$1',
                            '[img]$2[/img]',
                            '$2',
                            '\n',
                            '[b]$1[/b]',
                            '[url=$1]$3[/url]',
                            '[code]$1[/code]',
                            '[color="$1"]$2[/color]',
                            '[quote]$1[/quote]',   
                    );
 
            $text = preg_replace ($htmltags, $bbtags, $data);
            $text = strip_tags($text);
            $text = str_replace(array('\n','&nbsp;','&ndash;','&rdquo;','&ldquo;','&quot;'),array('','','-','','',''),$text);
 
            return $text;
             
        }
 
    ## direct insert to Database ##
    #smf_post($title,$bid,$authorid,$author,$views,$timestamp,$email,$ip,$smiley,$icon,$post_content,$prefix='smf');
    function smf1_post($subject,$bid,$authorid,$author,$views,$timestamp,$email,$ip,$smiley,$icon,$message,$prefix)
        {
        $this->query("SET NAMES UTF8");
        $this->query("INSERT INTO ".$prefix."_topics (ID_BOARD, ID_MEMBER_STARTED, ID_MEMBER_UPDATED, numViews) VALUES ('$bid', '$authorid', '$authorid', '$views')");
        $tid = $this->get_last_id($prefix."_topics","ID_TOPIC");
        $this->query("INSERT INTO ".$prefix."_messages (ID_TOPIC, ID_BOARD, posterTime, ID_MEMBER, subject, posterName, PosterEmail, posterIP, smileysEnabled, body, icon) VALUES ('$tid', '$bid', '$timestamp', '$authorid', '$subject', '$author', '$email', '$ip', '$smiley', '$message', '$icon')");
        $mid = $this->get_last_id($prefix."_messages","ID_MSG");
        $this->query("UPDATE ".$prefix."_topics SET ID_FIRST_MSG='$mid', ID_LAST_MSG='$mid' WHERE ID_TOPIC='$tid'");
        $this->query("UPDATE ".$prefix."_messages SET ID_MSG_MODIFIED='$mid' WHERE ID_MSG='$mid'");
        $this->query("UPDATE ".$prefix."_boards SET ID_LAST_MSG='$mid', ID_MSG_UPDATED='$mid', numTopics=numTopics+1, numPosts=numPosts+1 WHERE ID_BOARD='$bid'");
        $this->query("UPDATE ".$prefix."_members SET posts=posts+1 WHERE ID_MEMBER='$authorid'");
         
        }
         
    function smf2_post($subject,$bid,$authorid,$author,$views,$timestamp,$email,$ip,$smiley,$icon,$message,$prefix)
    {
    $this->query("INSERT INTO ".$prefix."_topics (ID_BOARD, ID_MEMBER_STARTED, ID_MEMBER_UPDATED, num_Views) VALUES ('$bid', '$authorid', '$authorid', '$views')");
    $tid = $this->get_last_id($prefix."_topics","ID_TOPIC");
    $this->query("INSERT INTO ".$prefix."_messages (ID_TOPIC, ID_BOARD, poster_Time, ID_MEMBER, subject, poster_Name, Poster_Email, poster_IP, smileys_Enabled, body, icon) VALUES ('$tid', '$bid', '$timestamp', '$authorid', '$subject', '$author', '$email', '$ip', '$smiley', '$message', '$icon')");
    $mid = $this->get_last_id("".$prefix."_messages","ID_MSG");
    $this->query("UPDATE ".$prefix."_topics SET ID_FIRST_MSG='$mid', ID_LAST_MSG='$mid' WHERE ID_TOPIC='$tid'");
    $this->query("UPDATE ".$prefix."_messages SET ID_MSG_MODIFIED='$mid' WHERE ID_MSG='$mid'");
    $this->query("UPDATE ".$prefix."_boards SET ID_LAST_MSG='$mid', ID_MSG_UPDATED='$mid', num_Topics=num_Topics+1, num_Posts=num_Posts+1 WHERE ID_BOARD='$bid'");
    $this->query("UPDATE ".$prefix."_members SET posts=posts+1 WHERE ID_MEMBER='$authorid'");
             
    }
 
 
    function query($data)
    {
        @mysql_query($data) or die(mysql_error());
    }
 
    function get_last_id($table,$data)
    {
        $query = @mysql_query("SELECT $data FROM $table ORDER BY $data DESC LIMIT 0,1") or die(mysql_error());
        $result = @mysql_fetch_object($query) or die(mysql_error());
        return ($result->$data);
    }
     
     
     
    ### Form Post ###
    ## smf_post($url,$user,$pass,'Putter','Hello','1');
    function smf_post($url,$user,$pass,$title,$post,$id)
        {
        //Login
        $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR,"./tmp/cookie");
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);  
            curl_setopt($ch, CURLOPT_URL, $url.'/index.php?action=login2');
            curl_setopt($ch, CURLOPT_POST, 1); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('user'=>$user,'passwrd'=>$pass,'cookieneverexp'=>'on')); 
 
            $data = curl_exec($ch); 
            curl_close($ch); 
             
            // get seq num and 
            $ch = curl_init(); 
                    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_COOKIEFILE,"./tmp/cookie");
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);  
                    curl_setopt($ch, CURLOPT_URL, $url.'/index.php?action=post;board='.$id.'.0');
         
                    $content = curl_exec($ch); 
                    curl_close($ch); 
             
                     
            //seqnum
            preg_match('/name="seqnum" value="(.+?)"/',$content,$seq);
            $seq = $seq[1];
 
            //sessionVar
            preg_match('/sSessionVar: \'(.+?)\'/',$content,$sesVar);
            $sesVar = $sesVar[1];
         
            //sessionID
            preg_match('/sSessionId: \'(.+?)\'/',$content,$sesID);
            $sesID = $sesID[1];
                     
         
        $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_COOKIEFILE,"./tmp/cookie");
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION,0);  
                curl_setopt($ch, CURLOPT_URL, $url.'/index.php?action=post2;start=0;board='.$id);
                curl_setopt($ch, CURLOPT_POST, 1); 
                curl_setopt($ch, CURLOPT_POSTFIELDS,array('topic'=>'0',
                        'subject'=>$title,
                        'icon'=>'xx',
                        'sel_face'=>'',
                        'sel_size' => '',
                        'sell_color'=>'',
                        'message'=>$post,
                        'message_mode'=>'0',
                        'notify'=>'0',
                        'lock'=>'0',
                        'sticky'=>'0',
                        'move'=>'0',
                        'additional_options'=>'0',
                        $sesVar => $sesID,
                        'seqnum' => $seq
                        )); 
                         
                $value = curl_exec($ch); 
                curl_close($ch); 
                 
                //debug
                //file_put_contents('debug.html',$value);
                 
            unlink('./tmp/cookie');
             
        }
    ############
     
    ### Form Post Version 1.1.x ###
    ## smf1_post($url,$user,$pass,'Putter','Hello','1');
        function smf1_post_form($url,$user,$pass,$title,$post,$id)
            {
            //Login
            $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_COOKIEJAR,"./tmp/cookie");
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);  
                curl_setopt($ch, CURLOPT_URL, $url.'/index.php?action=login2');
                curl_setopt($ch, CURLOPT_POST, 1); 
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('user'=>$user,'passwrd'=>$pass,'cookieneverexp'=>'on')); 
 
                $data = curl_exec($ch); 
                curl_close($ch); 
                 
                // get seq num and sessid
                $ch = curl_init(); 
                        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                        curl_setopt($ch, CURLOPT_HEADER, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_COOKIEFILE,"./tmp/cookie");
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);  
                        curl_setopt($ch, CURLOPT_URL, $url.'/index.php?action=post;board='.$id.'.0');
             
                        $content = curl_exec($ch); 
                        curl_close($ch); 
                 
                         
                //seqnum
                preg_match('/name="seqnum" value="(.+?)"/',$content,$seq);
                $seq = $seq[1];
 
             
                //sessionID
                preg_match('/name="sc" value="(.+?)"/',$content,$sesID);
                $sesID = $sesID[1];
                         
             
            $ch = curl_init(); 
                    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_COOKIEFILE,"./tmp/cookie");
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,0);  
                    curl_setopt($ch, CURLOPT_URL, $url.'/index.php?action=post2;start=0;board='.$id);
                    curl_setopt($ch, CURLOPT_POST, 1); 
                    curl_setopt($ch, CURLOPT_POSTFIELDS,array('topic'=>'0',
                            'subject'=>$title,
                            'icon'=>'xx',
                            'message'=>$post,
                            'notify'=>'0',
                            'lock' =>'0',
                            'goback' =>'1',
                            'sticky'=>'0',
                            'move'=>'0',
                            'post'=>'ตั้งกระทู้',
                            'additional_options'=>'0',
                            'sc' => $sesID,
                            'seqnum' => $seq
                            )); 
                             
                    $value = curl_exec($ch); 
                    curl_close($ch); 
                     
                    //debug
                    //file_put_contents('debug.html',$value);
                     
                unlink('./tmp/cookie');
                 
            }
        ############
 
}
 
 
?>