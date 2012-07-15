interaction-list-count
======================

Quick and dirty PHP script that consolidates and counts all of the unique interactions in a DL_POLY FIELD file.  

Only counts BOND, ANGLE, DIHEDRAL, and INVERSION interactions.

Reads a DL_POLY 4 FIELD file, and generates a list of all the interactions. 
Used for a quick check of whether the FIELD generated is correct.

Sample output line: 
  HA-CA-CA-HA (6): 2-11-3-4 2-1-11-12 4-3-5-6 6-5-7-8 8-7-9-10 10-9-11-1
This means that the FIELD file that generated this has 6 HA-CA-CA-HA 
interactions.  The list of numbers is which indices that make up these
interactions.


NOTE: FIELD file MUST contain the interactions in the following order:
   1. ATOM (This is not an interaction type, but yeah, still) 
   2. BOND
   3. ANGLE
   4. DIHEDRAL
   5. INVERSION (out-of-plane bending angle)

Also, this assumes that the ATOM section of the FIELD file lists every atom.
If the ATOM section of your FIELD file is something like this:
ATOM 8
C  12.011  1.2  2
H   1.008 -0.4  6

instead of like this:
ATOM 8
C  12.011  1.2  1
C  12.011  1.2  1
H   1.008 -0.4  1
H   1.008 -0.4  1
H   1.008 -0.4  1
H   1.008 -0.4  1
H   1.008 -0.4  1
H   1.008 -0.4  1

this script will not work correctly.

Also please note that splitter function is terrible and will not always work.
It should work in all cases concerning FIELD files, but I did not really test 
too hard any extreme cases.
