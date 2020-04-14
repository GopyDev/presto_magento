Requires a curl executable
(available for several platforms at http://curl.haxx.se/download.html,
usually already installed on many Linux distibutions)

To test (common):
1) sign in to Viamente Route Planner, click on "Account", click on "API Integration" and make sure that "Enable API Integration" is checked;
2) copy the API key;
3) make sure you have at least one saved group of drivers. To create a group of drivers check our KB article here:
http://support.viamente.com/customer/portal/articles/584707-save-your-drivers-as-a-group

On Windows:
4) download a copy of the curl executable (any "Win32 - Generic" binary build should be ok) and place it in this folder together with any accompanying dll files;
5) edit run.cmd replacing "YOUR_API_KEY_HERE" with the API key obtained at step 2;
6) launch "run.cmd".

On Linux:
4) download and install a copy of the curl executable for your distribution (from the console: "sudo apt-get install curl");
5) edit the run.sh file replacing "YOUR_API_KEY_HERE" with the API key obtained at step 2;
6) make sure the editor and/or the file copy process did not mess up the line endings (from the console: "dos2unix run.sh");
7) from the console launch "./run.sh".
