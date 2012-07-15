#!/usr/bin/php
<?php

/*
  Name: count.php
  By: Jeff Sung
  Last Updated: 04/03/2012 @ 18:16

  Reads a DL_POLY 4 FIELD file, and generates a list of all the interactions. 
  Used for a quick check of whether the FIELD generated is correct.

  Sample output line: 
    HA-CA-CA-HA (6): 2-11-3-4 2-1-11-12 4-3-5-6 6-5-7-8 8-7-9-10 10-9-11-1
  This means that the FIELD file that generated this has 6 HA-CA-CA-HA 
  interactions.  The list of numbers is which indices that make up these
  interactions.

  To run, just make sure this has executable permissions (chmod a+x count.php)
  Then just execute with ./count.php

***NOTE: FIELD file MUST contain the interactions in the following order:
   1. ATOM
   2. BOND
   3. ANGLE
   4. DIHEDRAL
   5. INVERSION (out-of-plane bending angle)

****NOTE: FIELD file must be called FIELD.  

****NOTE: Requires PHP to run.  PHP is not installed by default, I think.
*/

function splitter($original){
/* Splits a line into an array by spaces.  Trims.  
 * $original="    The   quick    brown fox jumps over the lazy dog" splits into
 * $str[0]="The"
 * $str[1]="quick"
 * $str[2]="brown"
 * etc.
 */
	$blah=explode(" ",$original);
	$str=array();
	for ($x=0; $x<sizeOf($blah); $x++){
		if (trim($blah[$x])) array_push($str,trim($blah[$x]));
	}
	return $str;
}

$infile=fopen("FIELD","r");
//1. Read in the FIELD header junk
for ($x=0; $x<5; $x++){
	fgets($infile);
}

//2. Get array of atoms
$line=fgets($infile);
$larray=splitter($line);
$natom=trim($larray[1]);
$atoms=array();
for ($x=0; $x<$natom; $x++){
	$line=fgets($infile);
	$temp=splitter($line);
	$atoms[$x]=$temp[0];
	//echo $atoms[$x]."\n";
}

//3. Get bonds
echo "Bonds:\n";
$line=fgets($infile);
$larray=splitter($line);
$nbond=trim($larray[1]);
$bonds=array();
for ($x=0; $x<$nbond; $x++){
	$line=fgets($infile);
	$temp=splitter($line);
	$at1=$atoms[$temp[1]-1];
	$at2=$atoms[$temp[2]-1];
	$value1="$at1-$at2";
	$value2="$at2-$at1";

	$flag=true;
	for ($y=0; $y<sizeOf($bonds); $y++){
		if ($bonds[$y]['what']==$value1||$bonds[$y]['what']==$value2){
			$flag=false;
			$bonds[$y]['count']++;	
			$bonds[$y]['which'].=$temp[1]."-".$temp[2]." ";
			break;
		}
	}
	if ($flag){
		$bondtemp=array();
		$bondtemp['what']=$value1;
		$bondtemp['count']=1;
		$bondtemp['which']=$temp[1]."-".$temp[2]." ";
		array_push($bonds,$bondtemp);
	}
}

for ($x=0; $x<sizeOf($bonds); $x++){
	echo $bonds[$x]['what']." (".$bonds[$x]['count']."): ".$bonds[$x]['which']."\n\n";
}
	
//4. Get angles
echo "Angles: \n";
$line=fgets($infile);
$larray=splitter($line);
$nangle=trim($larray[1]);
$angles=array();
for ($x=0; $x<$nangle; $x++){
	$line=fgets($infile);
	$temp=splitter($line);
	$at1=$atoms[$temp[1]-1];
	$at2=$atoms[$temp[2]-1];
	$at3=$atoms[$temp[3]-1];
	$type=$temp[0];
	$value1="$at1-$at2-$at3".($type=="cmps"?"*":"");
	$value2="$at3-$at2-$at1".($type=="cmps"?"*":"");

	$flag=true;
	for ($y=0; $y<sizeOf($angles); $y++){
		if ($angles[$y]['what']==$value1||$angles[$y]['what']==$value2){
			$flag=false;
			$angles[$y]['count']++;	
			$angles[$y]['which'].=$temp[1]."-".$temp[2]."-".$temp[3]." ";
			break;
		}
	}
	if ($flag){
		$angletemp=array();
		$angletemp['what']=$value1;
		$angletemp['count']=1;
		$angletemp['which']=$temp[1]."-".$temp[2]."-".$temp[3]." ";
		array_push($angles,$angletemp);
	}
}

for ($x=0; $x<sizeOf($angles); $x++){
	echo $angles[$x]['what']." (".$angles[$x]['count']."): ".$angles[$x]['which']."\n\n";
}

//4. Get dihedrals
echo "Inversions:\n";
$line=fgets($infile);
$larray=splitter($line);
$ndihedral=trim($larray[1]);
$dihedrals=array();
for ($x=0; $x<$ndihedral; $x++){
	$line=fgets($infile);
	$temp=splitter($line);
	$at1=$atoms[$temp[1]-1];
	$at2=$atoms[$temp[2]-1];
	$at3=$atoms[$temp[3]-1];
	$at4=$atoms[$temp[4]-1];
	$value1="$at1-$at2-$at3-$at4";
	$value2="$at4-$at3-$at2-$at1";

	$flag=true;
	for ($y=0; $y<sizeOf($dihedrals); $y++){
		if ($dihedrals[$y]['what']==$value1||$dihedrals[$y]['what']==$value2){
			$flag=false;
			$dihedrals[$y]['count']++;	
			$dihedrals[$y]['which'].=$temp[1]."-".$temp[2]."-".$temp[3]."-".$temp[4]." ";
			break;
		}
	}
	if ($flag){
		$dihedraltemp=array();
		$dihedraltemp['what']=$value1;
		$dihedraltemp['count']=1;
		$dihedraltemp['which']=$temp[1]."-".$temp[2]."-".$temp[3]."-".$temp[4]." ";
		array_push($dihedrals,$dihedraltemp);
	}
}

for ($x=0; $x<sizeOf($dihedrals); $x++){
	echo $dihedrals[$x]['what']." (".$dihedrals[$x]['count']."): ".$dihedrals[$x]['which']."\n\n";
}

//5. Get inversions
echo "Dihedrals:\n";
$line=fgets($infile);
$larray=splitter($line);
$nimproper=trim($larray[1]);
$impropers=array();
for ($x=0; $x<$nimproper; $x++){
	$line=fgets($infile);
	$temp=splitter($line);
	$at1=$atoms[$temp[1]-1];
	$at2=$atoms[$temp[2]-1];
	$at3=$atoms[$temp[3]-1];
	$at4=$atoms[$temp[4]-1];
	$value1="$at1-$at2+$at3-$at4";
	$value2="$at4-$at3+$at2-$at1";

	$flag=true;
	for ($y=0; $y<sizeOf($impropers); $y++){
		if ($impropers[$y]['what']==$value1||$impropers[$y]['what']==$value2){
			$flag=false;
			$impropers[$y]['count']++;	
			$impropers[$y]['which'].=$temp[1]."-".$temp[2]."+".$temp[3]."-".$temp[4]." ";
			break;
		}
	}
	if ($flag){
		$impropertemp=array();
		$impropertemp['what']=$value1;
		$impropertemp['count']=1;
		$impropertemp['which']=$temp[1]."-".$temp[2]."+".$temp[3]."-".$temp[4]." ";
		array_push($impropers,$impropertemp);
	}
}

for ($x=0; $x<sizeOf($impropers); $x++){
	echo $impropers[$x]['what']." (".$impropers[$x]['count']."): ".$impropers[$x]['which']."\n\n";
}

fclose($infile);
?>
