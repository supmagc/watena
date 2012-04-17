                   <?php
require_once('curl.php');

$gebruikersnaam = '105238180871871114280';

$curl = new CURL();
$curl->enableCache();

echo '<a name="fotoalbum"></a>';

if(isset($_GET['album']) && !empty($_GET['album']))
{    
    $xml = $curl->get('http://picasaweb.google.com/data/feed/api/user/'.$gebruikersnaam.'/albumid/'.$_GET['album']);
    
    echo $xml;
    
    if($xml != 'No album found.')
    {
        $xml = new SimpleXMLElement($xml);
    
        echo '>> <a href="index.php?pagina=fotos#fotoalbum" class="text_darkblue_12">Fotoalbum</a> >> <a href="index.php?pagina=fotos&amp;album='.$_GET['album'].'#fotoalbum" class="text_darkblue_12">'.$xml->title.'</a> (<a href="'.$xml->link[2]['href'].'" target="_blank" class="text_darkblue_12">Presentatie</a>)<br>';
    
    if(count($xml->entry) > 0)
    {
            echo '<hr><div style="text-align: center">';
            
            if(isset($_GET['foto']) && !empty($_GET['foto']))
            {
                for ($i = 0; $i <= count($xml->entry)-1; $i++)
                {
                    if($xml->entry[$i]->title == $_GET['foto'])
                    {
                        $tmp = $i;
                    }
                }
                
                if($tmp == 0)
                {
                    echo '<< Vorige';
                }else{
                    echo '<< <a href="index.php?pagina=fotos&amp;album='.$_GET['album'].'&amp;foto='.$xml->entry[$tmp-1]->title.'#fotoalbum" class="text_darkblue_12">Vorige</a>';
                }
                
                echo '&nbsp;&nbsp; Foto '.($tmp+1).' van '.count($xml->entry).' &nbsp;&nbsp;';
                
                if($tmp == count($xml->entry)-1)
                {
                    echo 'Volgende >><br><br>';
                }else{
                    echo '<a href="index.php?pagina=fotos&amp;album='.$_GET['album'].'&amp;foto='.$xml->entry[$tmp+1]->title.'#fotoalbum" class="text_darkblue_12">Volgende</a> >><br><br>';
                }

                echo '<img src="'.$xml->entry[$tmp]->content['src'].'?imgmax=576" title="'.$xml->title.' - '.$xml->entry[$tmp]->title.'" alt="'.$xml->title.' - '.$xml->entry[$tmp]->title.'">';  
            }else{
                   foreach($xml->entry as $album)
                {                    
                    echo '<a href="index.php?pagina=fotos&amp;album='.$_GET['album'].'&amp;foto='.$album->title.'#fotoalbum" class="thumbnail"><img src="'.$album->content['src'].'?imgmax=400" class="thumbnail" title="'.$album->title.'" border="0" alt="'.$album->title.'"></a><br>';
                }
            }
            
            echo '</div>';
            
    }else{
        echo '<p>Sorry, er zijn op dit moment nog geen fotos aan dit album toegevoegd</p>';
    }
    }else{
    echo '<p>Sorry, ik heb dit album niet kunnen vinden!</p>';
    }
}else{
    echo '<p>Uw fotoalbum hieronder? Neem contact met me op.</p>';
    
    $xml = $curl->get('http://picasaweb.google.com/data/feed/api/user/'.$gebruikersnaam);

    $xml = new SimpleXMLElement($xml);
    
    if(count($xml->entry) > 0)
    {
    foreach ($xml->entry as $album)
    {
        echo '<img src="images/folder.gif" style="vertical-align : middle; margin-right: 10px;">';
        echo '<b><a href="index.php?pagina=fotos&amp;album='.strtolower(str_replace(' ','',$album->title)).'#fotoalbum" class="text_darkblue_12">'.$album->title.'</a></b>';
        echo ' Laatst geupdate op '.strftime("%e %B %Y om %H:%M", strtotime($album->updated)).'<br>';
    }
    }else{
    echo '<p>Sorry, er zijn op dit moment nog geen albums toegevoegd!</p>';
    }
}
?>