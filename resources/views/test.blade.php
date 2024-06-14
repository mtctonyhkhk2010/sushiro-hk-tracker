<head>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, initial-scale=1, user-scalable=no" />
    <meta charset="utf-8">

    <link rel="stylesheet" href="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/style.min.css" type="text/css">
    <script src="https://developer.api.autodesk.com/modelderivative/v2/viewers/7.*/viewer3D.min.js"></script>

    <style>
        body {
            margin: 0;
        }
        #forgeViewer {
            width: 100%;
            height: 100%;
            margin: 0;
            background-color: #F0F8FF;
        }

        #navTools, #toolbar-cameraSubmenuTool, #toolbar-measurementSubmenuTool, #toolbar-sectionTool, #settingsTools {
            display: none !important;
        }
    </style>
</head>
<body>

<div id="forgeViewer"></div>

<script>
    let viewer;
    const options = {
        env: 'AutodeskProduction2',
        api: 'streamingV2',  // for models uploaded to EMEA change this option to 'streamingV2_EU'
        getAccessToken: function(onTokenReady) {
            const token = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjY0RE9XMnJoOE9tbjNpdk1NU0xlNGQ2VHEwUV9SUzI1NiIsInBpLmF0bSI6ImFzc2MifQ.eyJzY29wZSI6WyJjb2RlOmFsbCIsImRhdGE6d3JpdGUiLCJkYXRhOnJlYWQiLCJidWNrZXQ6Y3JlYXRlIiwiYnVja2V0OmRlbGV0ZSIsImJ1Y2tldDpyZWFkIl0sImNsaWVudF9pZCI6ImVMSHFZdUFTN21rTXZVOW5GRldyTktTbnJobG1IbWtwc1dwV0dlSUlJclFvaVhRMiIsImlzcyI6Imh0dHBzOi8vZGV2ZWxvcGVyLmFwaS5hdXRvZGVzay5jb20iLCJhdWQiOiJodHRwczovL2F1dG9kZXNrLmNvbSIsImp0aSI6Ik1zSm5ZUjM2Um1aMzZ1cnQ1QUNsSjhYeExsaERpMkNPYXZPMTNFWHRtb0xwR1VUdmVaemZUc09WMlZUNndVWjEiLCJleHAiOjE3MTgzNTI1NTZ9.cqJTGdo6i5_17DJKkEMxTu3a5czTAl2MnN6pU9YVApomWhD230silGveqkjLxzlMrqJsmU-vUclYKwYeAIP9ZxEarDXjAjP4H_d3Zq4K6L9275w3sbbqlECDmFbN1sKYI2HZx6ONyI99SswsVFSWvTqXFSHXWMYovBGkEhBY_DtUn9OEJAEDvhiaDtLKsSmS-aamW7eOYO2dn9KAXi8A3bcd4rEIHkefBkkZSCPqtgstxGeeBh33LoXMfJUh-i6eydlLufe_B2n9FDju8c7JOxte_Nb-HrWL8_QVCO5Bmry53ZE81iBKfSZe9JRlUkCqup-Y61xFvxUiQHLN65gv6g';
            const timeInSeconds = 3600; // Use value provided by APS Authentication (OAuth) API
            onTokenReady(token, timeInSeconds);
        }
    };

    Autodesk.Viewing.Initializer(options, function() {

        const htmlDiv = document.getElementById('forgeViewer');
        viewer = new Autodesk.Viewing.GuiViewer3D(htmlDiv);

        const documentId = 'urn:dXJuOmFkc2sub2JqZWN0czpvcy5vYmplY3Q6ZGFpbHlzdXBwbHkuZG5wLzIwMjQwNTE1LWFzbS1hMl9hc20uc3Rw';
        Autodesk.Viewing.Document.load(documentId, onDocumentLoadSuccess, onDocumentLoadFailure);

        function onDocumentLoadSuccess(viewerDocument) {
            const defaultModel = viewerDocument.getRoot().getDefaultGeometry();
            viewer.loadDocumentNode(viewerDocument, defaultModel);
        }

        function onDocumentLoadFailure() {
            console.error('Failed fetching Forge manifest');
        }


        const startedCode = viewer.start();
        if (startedCode > 0) {
            console.error('Failed to create a Viewer: WebGL not supported.');
            return;
        }

        console.log('Initialization complete, loading a model next...');

        viewer.addEventListener(Autodesk.Viewing.TOOLBAR_CREATED_EVENT, (e) => {
            const settingsTools = viewer.toolbar.getControl('settingsTools')
            settingsTools.removeControl('toolbar-fullscreenTool')
        })

    });
</script>
</body>
