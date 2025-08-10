<script>
   
    var mainLayout, mainToolbar;
    var dvMenu;
    var html;
    var logoSVG, dataToolbar;
    var canRead, canCreate, canModify, canDelete = false;

    mainLayout = new dhx.Layout("layout", {
        type: "line",
        rows: [
            { id: "header", html: "1", height: "60px" },
            { id: "workplace", html: "" },
        ]    
    });

    logoSVG = "<img src='../images/logo_small.png'>";
    dataToolbar = [
      {
        id: "logo",
        type: "customHTML",
        html: logoSVG,
        css: "logo-container",
        width: "50%",
      },
      {
        type: "spacer"
      },
      {
        id: "selProg",
        type: "text",
        icon: "",
        value: ""
      },
      {
        type: "separator"
      },
      {
          "id": "other",
          "type": "button",
          "view": "link",
          "circle": true,
          "color": "secondary",
          "icon": "mdi mdi-application-cog"
      },      
      {
          "id": "avatar",
          "type": "imageButton",
          "src": "../images/imgUsers/noImage.png",
      }
    ];

    mainToolbar = new dhx.Toolbar(null, {
      data: dataToolbar
    });
    mainToolbar.events.on('click', function(id,e) {
      console.log(id);
      if ( id == 'logo' ) window.open('main.php','_self');
    })
    mainLayout.getCell("header").attach(mainToolbar);

    function setHeader() {
        var fileName = location.pathname.split("/").slice(-1);
        console.log(fileName);
        dhx.ajax.get("menuQy.php?t=data&p="+fileName).then(function (data) {
            obj = JSON.parse(data);
            console.log(obj.name+" - "+obj.icon);

            prog = '{"value": "'+obj.name+'", "icon": "'+obj.icon+'"}';
            mainToolbar.data.update("selProg", JSON.parse(prog));
            prog = '{"src": "../images/imgUsers/'+obj.avatar+'"}';
            mainToolbar.data.update("avatar", JSON.parse(prog));

         }).catch(function (err) {
            console.log(err);
        });
    }

    setHeader();
    

</script>
