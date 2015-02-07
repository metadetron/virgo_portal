open IN, "pl-PL_virgo.ini" or die $!;
open OUT, ">pl-PL_virgo.sql" or die $!;

while (<IN>) { 
	if ($_ =~ m/#.*/) {
	} else {
		if ($_ =~ /^(.*)=(.*)$/) {
			$nam = $1;
			$val = $2;
			$val =~ s/'/''/g;
	  		print OUT "INSERT INTO prt_translations (trn_lng_id, trn_token, trn_text) SELECT lng_id, '$nam', '$val' FROM prt_languages WHERE lng_default = 1;\n"; 
  		}
	}
}

close OUT;
close IN;
