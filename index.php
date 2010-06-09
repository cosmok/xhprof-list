<?php
require dirname(__FILE__) . '/conf.php';
if(!is_dir(XHPROF_DATA_DIR)) {
    echo "XHPROF_DATA_DIR in conf.php has not been set to a valid directory";
    exit(1);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>xhprof list</title>
    <style type="text/css" media="screen">
            @import "media/css/demo_page.css";
            @import "media/css/demo_table.css";
            #content {
                width: 800px ;
                margin-left: auto ;
                margin-right: auto ;
            }
    </style>
    <script type="text/javascript" src="media/js/jquery.js"></script>
    <script type="text/javascript" src="media/js/jquery.dataTables.min.js"></script>
</head>
<body>
<?php
echo "<div id='content'>";
echo "<table id='list' width='100%'>
      <thead>
      <tr>
      <th>Namespace</th>
      <th>Functions</th>
      <th>Callgraph</th>
      <th>Modified Datetime</th>
      <th>Modified Timestamp</th>
      </tr>
      </thead>
      <tbody>";
if ($handle = opendir(XHPROF_DATA_DIR)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $info = pathinfo(XHPROF_DATA_DIR . '/'. $file);
            echo "<tr>"; 
            echo "<td>" . $info['extension'] ."</td>";
            echo "<td>" . '<a href="'. XHPROF_URL .'/?run=' . $info['filename'] . '&amp;source=' . $info['extension'] . '">Functions</a></td>';
            echo '<td><a href="'. XHPROF_URL .'/callgraph.php?run=' . $info['filename'] . '&amp;source=' . $info['extension'] . '">Callgraph</a></td>';
            echo "<td>" . date("F d Y H:i:s", filectime(XHPROF_DATA_DIR . '/'. $file)) ."</td>";
            echo "<td>" . filemtime(XHPROF_DATA_DIR . '/'. $file) ."</td>";
            echo "</tr>";
        }
    }
    closedir($handle);
}
echo "</tbody>";
echo "</table>";
echo "</div>";
?>
<script type="text/javascript">
$(document).ready(function(){
    oTable = $('#list').dataTable({
"fnDrawCallback": function ( oSettings ) {
                        if ( oSettings.aiDisplay.length == 0 )
                        {
                            return;
                        }
                        
                        var nTrs = $('#list tbody tr');
                        var iColspan = nTrs[0].getElementsByTagName('td').length;
                        var sLastGroup = "";
                        for ( var i=0 ; i<nTrs.length ; i++ )
                        {
                            var iDisplayIndex = oSettings._iDisplayStart + i;
                            var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[0];
                            if ( sGroup != sLastGroup )
                            {
                                var nGroup = document.createElement( 'tr' );
                                var nCell = document.createElement( 'td' );
                                nCell.colSpan = iColspan;
                                nCell.className = "group";
                                nCell.innerHTML = sGroup;
                                nGroup.appendChild( nCell );
                                nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
                                sLastGroup = sGroup;
                            }
                        }
                    },
                    "aoColumns": [
                        { "bVisible": false },
                        null,
                        null,
                        null,
                        { "bVisible": false , "aaSorting": ["desc"]}
                    ],
                    "bStateSave": true,
                    "iDisplayLength": 25
                });

});
</script>
</body>
</html>
