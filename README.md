# Enterprise-plugin-wwAuthImportExport
Export/Import Authentications, User Groups and Access Profiles to/from .csv files

--- NEEDS REWRITING BUT DOES THE JOB FOR NOW ---

Enterprise 9+

In Enterprise 9 and better you have full functionality. The Integrations admin page shows two new icons, one for exporting/importing the authorizations, the other for the access profiles.

PastedGraphic-1.png

The Acces profiles export/import is easy:

- it shows the access profile export already in a text frame
- you can copy/paste parts from there, or you can use the Export button to save it as a csv text file
- use Choose file to select a previously exported and edited file for import
note: the current version does not clear the access profile before importing it again
- you can split up the exported file to define different access profiles and you can import them one by one


Authorizations also has an Export and Choose file button for the same purpose, but it also has a Reset button, which (without asking!) clears all Authorizations except for the default Admin group.


Enterprise 7,8

After having installed the plug-in, you first have to login your browser into the web admin to have a valid session. 
Then call up:

<server>/Enterprise/config/plugins/AuthImportExport/index.php

You get two buttons which do exactly what they tell.

The procedure has to be as follows:
- Export profiles from the old system
- Export authorizations from the old system
- Edit the export files to your needs (you can split them up into multiple files if you like)
- Import the access profiles into the new system
- Import the authorizations into the new system
