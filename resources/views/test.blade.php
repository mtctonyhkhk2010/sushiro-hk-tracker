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
            const token = '{{ $access_token }}';
            const timeInSeconds = 3600; // Use value provided by APS Authentication (OAuth) API
            onTokenReady(token, timeInSeconds);
        }
    };

    Autodesk.Viewing.Initializer(options, function() {

        const htmlDiv = document.getElementById('forgeViewer');
        viewer = new Autodesk.Viewing.GuiViewer3D(htmlDiv);

        const documentId = 'dXJuOmFkc2sub2JqZWN0czpvcy5vYmplY3Q6ZGFpbHlzdXBwbHkuZG5wLzIwMjQwNjE0LWFzbS1hMl9hc20uU1RFUA';
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
