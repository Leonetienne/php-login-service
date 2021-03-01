<?php
	# Key derivative function for password hashing
	function HashPasswordKDF($pw, $username)
	{
		// First, add some salt
		$tmp = hash('sha512', $pw . 'uqP@a!2dR#Nqk79JnE&kGf7en2%E$E7jdr@mqqDSyn7W%hu^@tGUn%&H5c%&&B' . $username);

		// Then, stir it
		$numIts = (strlen($pw) * strlen($username)) + 10;
		for ($i = 0; $i < $numIts; $i++) {
			
			if ($i % 2 == 0)
				$tmp = hash('sha512', $tmp); // Hash result again
			else if ($i % 11 != 0)
				$tmp = hash('sha512', strrev($tmp)); // Hash result again, but reverse it first

			if ($i % 16 == 0) { // If multiple of 16, hash twice
				$tmp = hash('sha512', $tmp); 
				$tmp = hash('sha512', $tmp); 
			}

			if ($i % 31 == 0) { // If multiple of 31, swap two sudo-random bytes
				$a = min($i / 31, 127);
				$b = ($i*$i) % 128;

				$c = $tmp[$a];
				$tmp[$a] = $tmp[$b];
				$tmp[$b] = $c;
			}
		}

		// Serve it hot and fresh
		return $tmp;
	}
?>
