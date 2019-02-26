<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Editor</title>
</head>
<body style="height:1000px;">
<div id="placeholder"></div>
<script type="text/javascript" src="http://localhost:8080/web-apps/apps/api/documents/api.js"></script>
<script>
    new DocsAPI.DocEditor("placeholder", {
        "document": {
            "fileType": "docx",
            "key": "<?php echo $documentId; ?>",
            "title": "Result Resume",
            "url": "<?php echo $fileUrl; ?>",
            "permissions": {
                "comment": false,
                "download": false,
                "edit": true,
                "fillForms": true,
                "print": true,
                "review": true
            },
        },
        "documentType": "text",
        "editorConfig": {
            "callbackUrl": "<?php echo $callBack; ?>",
            "customization": {
                "autosave": true,
                "chat": false,
                "commentAuthorOnly": false,
                "compactToolbar": true,
                "forcesave": false,
                "help": true
            },
            "user": {
                "id": "78e1e841",
                "name": "John Smith"
            }
        }
    });
</script>
</body>
</html>