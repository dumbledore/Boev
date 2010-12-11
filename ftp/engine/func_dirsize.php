<?php

// ------------ lixlpixel recursive PHP functions -------------
// recursive_directory_size( directory, human readable format )
// expects path to directory and optional TRUE / FALSE
// PHP has to have the rights to read the directory you specify
// and all files and folders inside the directory to count size
// if you choose to get human readable format,
// the function returns the filesize in bytes, KB and MB
// ------------------------------------------------------------

// to use this function to get the filesize in bytes, write:
// recursive_directory_size('path/to/directory/to/count');

// to use this function to get the size in a nice format, write:
// recursive_directory_size('path/to/directory/to/count',TRUE);

function recursive_directory_size($directory, $format=FALSE)
{
	$size = 0;

	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory) || !is_readable($directory))
	{
		// ... we return -1 and exit the function
		return -1;
	}
	// we open the directory
	if($handle = opendir($directory))
	{
		// and scan through the items inside
		while(($file = readdir($handle)) !== false)
		{
			// we build the new path
			$path = $directory.'/'.$file;

			// if the filepointer is not the current directory
			// or the parent directory
			if($file != '.' && $file != '..')
			{
				// if the new path is a file
				if(is_file($path))
				{
					// we add the filesize to the total size
					$size += filesize($path);

				// if the new path is a directory
				}elseif(is_dir($path))
				{
					// we call this function with the new path
					$handlesize = recursive_directory_size($path);

					// if the function returns more than zero
					if($handlesize >= 0)
					{
						// we add the result to the total size
						$size += $handlesize;

					// else we return -1 and exit the function
					}else{
						return -1;
					}
				}
			}
		}
		// close the directory
		closedir($handle);
	}
	// if the format is set to human readable
	if($format == TRUE)
	{
		// if the total size is bigger than 1 MB
		if($size / 1048576 > 1)
		{
			return round($size / 1048576, 1).' MB';

		// if the total size is bigger than 1 KB
		}elseif($size / 1024 > 1)
		{
			return round($size / 1024, 1).' KB';

		// else return the filesize in bytes
		}else{
			return round($size, 1).' bytes';
		}
	}else{
		// return the total filesize in bytes
		return $size;
	}
}
// ------------------------------------------------------------

?>