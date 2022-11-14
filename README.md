# Enterprise-plugin-wwAuthImportExport
Export/Import Authentications, User Groups and Access Profiles to/from .csv files

--- NEEDS REWRITING BUT DOES THE JOB FOR NOW ---

Studio Server 10.7+

In Studio Server 10.7 and better you have full functionality. The Integrations admin page shows two new icons, one for exporting/importing the authorizations, the other for the access profiles.

PastedGraphic-1.png

The Acces profiles export/import is easy:

- it shows the access profile export already in a text frame
- you can copy/paste parts from there, or you can use the Export button to save it as a csv text file
- use Choose file to select a previously exported and edited file for import
note: the current version does not clear the access profile before importing it again
- you can split up the exported file to define different access profiles and you can import them one by one


Authorizations also has an Export and Choose file button for the same purpose, but it also has a Reset button, which (without asking!) clears all Authorizations except for the default Admin group.
