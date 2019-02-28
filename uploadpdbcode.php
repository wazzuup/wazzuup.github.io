<?php
session_start();
include('header&menu.php');

function recursiveDelete($dir)
{
   if ($handle = @opendir($dir))
   {
     while (($file = readdir($handle)) !== false)
     {
         if (($file == ".") || ($file == ".."))
         {
           continue;
         }
         if (is_dir($dir . '/' . $file))
         {
           // call self for this directory
           recursiveDelete($dir . '/' . $file);
         }
         else
         {
           unlink($dir . '/' . $file); // remove this file
         }
     }
     @closedir($handle);
     rmdir ($dir);  
   }
} 

?>


<div class = "mainbod">
	<img src = "images/subtitle_processing.jpg" border = 0><br><br><br><br>
	<?php
	extract($_POST);

	#if ($pdbcode != "")
	if( preg_match("/^[\w\d]{4}$/",$pdbcode) )
	{
	  echo 'Code Submitted.  Processing...<br><br>';

	$_SESSION['pdbfilename'] = $pdbcode;

#	$_SESSION['phlev'] = $phlevel;

	$pdbcode = strtoupper($pdbcode);

#	  echo 'http://www.rcsb.org/pdb/cgi/export.cgi?job=download;pdbId=' . $pdbcode . '&opt=show&format=PDB<br><br><br>';


	if (!file_exists('uploads/' . $_SESSION['username'] . '/' . $_SESSION['pdbfilename'] . '/'))
	{
		mkdir('uploads/' . $_SESSION['username'] . '/' . $_SESSION['pdbfilename'] . '/');
	}

#		$file = 'http://www.rcsb.org/pdb/cgi/export.cgi?job=download;pdbId=' . $pdbcode . '&opt=show&format=PDB';

      $pdbcode = strtolower($pdbcode);
      $pdbmid = $pdbcode{1} . $pdbcode{2};
		$file = 'ftp://ftp.wwpdb.org/pub/pdb/data/structures/divided/pdb/' . $pdbmid . '/pdb' . $pdbcode . '.ent.gz';
#		$file = 'ftp://ftpbeta.rcsb.org/pub/data_by_method/all_exp_methods/biological_units/pdb/' . $pdbmid . '/' . $pdbcode . '.pdb1.gz';

#changed again!!!!!! old file below
		#$file = 'http://www.rcsb.org/pdb/cgi/export.cgi/1ZNI.pdb?job=download;pdbId=' . $pdbcode . ';page=0;opt=show;format=PDB;pre=1&compression=None';


# OLD FILE BELOW, MAY NEED AGAIN

		#$file = 'http://www.rcsb.org/pdb/cgi/export.cgi/' . $pdbcode . '.pdb?job=download;pdbId=' . $pdbcode . ';page=0;opt=show;format=PDB;pre=1&compression=None';


		$file2 = 'uploads/' . $_SESSION['username'] . '/' . $_SESSION['pdbfilename'] . '/' . $_SESSION['pdbfilename'] . '.pdb.gz';



	$web = @fopen($file,"r");
	$fout = fopen($file2, "w");

	if( !($web) || !($fout) ){

		print "<br><b>Unable to find pdb code \"$pdbcode\"\n</b><br><a href=\"uploadpdb.php\">Try a new pdb code.</a>";

		exit;
	}

	while(!feof($web))
	{
		$buffer = fgets($web, 10240000);
		fputs($fout, $buffer);
	}
	fclose($web);
	fclose($fout);

   #unzip the file we got from the ftp site
   $cmd = 'gunzip ' . $file2;
   exec($cmd);

   #reset the file to its unzipped version
	$file2 = 'uploads/' . $_SESSION['username'] . '/' . $_SESSION['pdbfilename'] . '/' . $_SESSION['pdbfilename'] . '.pdb';

	$size = filesize($file2);
        $_SESSION['filetype'] = "pdb";
	

	if ($size !=0)
		{
		echo 'Completed file copy. starting processing of file....';

		$url = "preprocess.php"; // target of the redirect
		$delay = "3"; // 1 second delay

		echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'">';
		}

	else
		{
		echo 'The file was not found on the PDB server.';
		}



	}
	else
	{

                print "<br><b>The sequence you entered \"$pdbcode\" is not a valid PDB code. \n</b><br><a href=\"uploadpdb.php\"> You need to enter a valid PDB code </a>";

	}


	?>
</div>

