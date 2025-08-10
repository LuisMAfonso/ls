<script>
   
    var mainLayout, mainToolbar;
    var sidebar;
    var html;
    var logoSVG, dataToolbar;
    var canRead, canCreate, canModify, canDelete = false;

    mainLayout = new dhx.Layout("layout", {
        type: "line",
        rows: [
            { id: "header", html: "1", height: "60px" },
            {
                type: "line",
                cols: [
                    { id: "sidebar", html: "", width: "200px" },
                    { id: "workplace", html: '<div id="root"></div>', },
                ]
            },
        ]    
    });

    logoSVG = "<img src='images/logo_small.png'>";
    dataToolbar = [
      {
        type: "customHTML",
        html: logoSVG,
        css: "logo-container"
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
          "id": "profile",
          "type": "button",
          "view": "link",
          "circle": true,
          "color": "secondary",
          "icon": "mdi mdi-application-cog"
      },      
      {
          "id": "avatar",
          "type": "imageButton",
          "src": "images/imgUsers/noImage.png",
      }
    ];

    mainToolbar = new dhx.Toolbar(null, {
      data: dataToolbar
    });
    mainToolbar.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'avatar' ) {
            wMessage = {
                header: "Logout ", text: "Do you want to logout?", buttons: ["no", "yes"], buttonsAlignment: "center",
            };   
            dhx.confirm(wMessage).then(function(answer){
                if (answer) {
                    console.log(answer);
                    window.location.href = "/ls";
                }
            });         
        }
        if ( id == 'profile' ) editProfile();
    });
    mainLayout.getCell("header").attach(mainToolbar);

    function setHeader() {
        var fileName = location.pathname.split("/").slice(-1);
        console.log(fileName);
        dhx.ajax.get("menuQy.php?t=data&p="+fileName).then(function (data) {
            obj = JSON.parse(data);
            console.log(obj.name+" - "+obj.icon);

            prog = '{"value": "'+obj.name+'", "icon": "'+obj.icon+'"}';
            mainToolbar.data.update("selProg", JSON.parse(prog));
            prog = '{"src": "images/imgUsers/'+obj.avatar+'"}';
            mainToolbar.data.update("avatar", JSON.parse(prog));

         }).catch(function (err) {
            console.log(err);
        });
    }

    sidebar = new dhx.Sidebar(null, {
      css: "dhx_widget--border_right"
    });

    sidebar.data.load("menuQy.php?t=main")
      .then(function () {
      sidebar.data.add({
        id: "toggle",
        css: "toggle-button",
        icon: "mdi mdi-backburger",
      }, sidebar.data.getIndex("dashboard"));

    });

    sidebar.events.on("click", function(id){
        const item = sidebar.data.getItem(id);
//        console.log(id+" - "+item.call);
        if ( id === "toggle" ){
            const toggleItem = sidebar.data.getItem("toggle");

            sidebar.toggle();

            if(sidebar.config.collapsed){
              toggleItem.icon = "mdi mdi-menu-close";
              mainLayout.getCell("sidebar").config.width = "44px";
              mainLayout.getCell("sidebar").paint();
            }
            else {
              toggleItem.icon = "mdi mdi-backburger";
              mainLayout.getCell("sidebar").config.width = "200px";
              mainLayout.getCell("sidebar").paint();
            }
        } else if ( item.call != 'menu' && item.call != '' ) {
            window.location.href = "./"+item.call;
        }
    });

    function editProfile() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Profile"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                {
                    type: "fieldset",
                    name: "personal",
                    label: "Personal info",
                    rows: [
                        { type: "input", name: "id", required: true, label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                        { type: "input", name: "name", required: true, label: "Name", labelPosition: "left", labelWidth: "80px", },
                        { 
                            align: "between",
                            cols: [
                                { type: "datepicker", name: "birthday", label: "Birthdate", labelPosition: "left", labelWidth: "80px", dateFormat: "%Y-%m-%d", },
                                { type: "spacer", width: "20px", },
                                { type: "avatar", name: "avatar", label: "Photo", icon: "dxi dxi-person", fieldName: "file",
                                    alt: "Employee photo", labelPosition: "left", target: "uploader.php", value: ""
                                },
                            ]
                        },
                       ]
                },
                {
                    type: "fieldset",
                    name: "account",
                    label: "Account info",
                    rows: [
                        { type: "input", name: "userId", required: true, label: "UserId", labelWidth: "80px", labelPosition: "left", },
                        { type: "input", name: "email", required: true, label: "Email", labelWidth: "80px", labelPosition: "left", validation: "email", },
                        { 
                            align: "start",
                            cols: [
                                { type: "text", name: "text1", required: false,
                                    label: "Password", labelWidth: "80px", labelPosition: "left" },
                                { type: "button", name: "changePW", icon: "mdi mdi-key-change", text: "Change password", url: "" },                        
                            ]
                        },
                     ]
                },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "edit", view: "flat", text: "Edit", },
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                }
            ]
        });
        form.getItem("avatar").events.on("afterShow", value => {
            console.log("afterShow", value);
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'changePW' ) changePassword();
            if ( name == 'edit' ) {
                form.getItem("edit").hide();
                form.getItem("cancel").show();
                form.getItem("send").show();
//                form.setProperties("password", { required: false,  });
//                form.getItem("password").disable();
                form.setProperties("userId", { required: true,  });
                form.getItem("userId").enable();
                form.setProperties("name", { required: true,  });
                form.getItem("name").enable();
                form.getItem("birthday").enable();
                form.getItem("avatar").enable();
                form.setProperties("email", { required: true,  });
                form.getItem("email").enable();

            }
            if ( name == 'cancel' ) {
                const config = {
                    header: "User ",
                    text: "Confirm cancelation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "User ",
                    text: "Confirm update?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("userMntWr.php?t=f", "POST").then(function(data){
    //                            message = JSON.parse(data);
                            console.log(data);
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        dhx.ajax.get("userMntQy.php?t=profile").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            form.setValue(obj);
            if ( obj.id > 0 ) { 
                form.getItem("cancel").hide();
                form.getItem("send").hide();
                form.setProperties("password", { required: false,  });
//                form.getItem("password").disable();
//                form.setProperties("userId", { required: false,  });
                form.getItem("userId").disable();
                form.setProperties("name", { required: false,  });
                form.getItem("name").disable();
                form.getItem("birthday").disable();
                form.getItem("avatar").disable();
                form.setProperties("email", { required: false,  });
                form.getItem("email").disable();
            }
        }).catch(function (err) {
                console.log(err);
        });

        dhxWindow.attach(form);
        dhxWindow.show();

    }

    function changePassword() {
        const dhxWindow = new dhx.Window({
            width: 540,
            height: 310,
            closable: true,
            movable: true,
            modal: true,
            title: "Change password"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", inputType: "password", name: "actualPW", required: true, label: "Current password", labelWidth: "140px", labelPosition: "left" },
                { type: "input", inputType: "password", name: "newPW1", required: true, label: "New password", labelWidth: "140px", labelPosition: "left" },
                { type: "input", inputType: "password", name: "newPW2", required: true, label: "Verify new password", labelWidth: "140px", labelPosition: "left" },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Change", submit: true, },
                    ]
                }
            ]
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Password change ",
                    text: "Cancel password change?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "Password change ",
                    text: "Confirm change?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const postData = {
                            actualPW: form.getItem('actualPW').getValue(),
                            newPW1: form.getItem('newPW1').getValue(),
                            newPW1: form.getItem('newPW2').getValue()
                        };
                        dhx.ajax.post("userMntWr.php?t=pw", postData).then(function(data){
                            msg = JSON.parse(data);
                            console.log(data);
                            if ( msg.origOK == 0 ) {
                                const cError = {
                                    header: "Change status ",
                                    text: "Current password wrong",
                                    buttons: ["OK"],
                                    buttonsAlignment: "center"
                                };     
                                dhx.alert(cError).then(function(answer){
                                    if (answer) {
                                    }
                                });
                            } 
                            if ( msg.origOK == 1 && msg.newMatch == 1) {
                                const cError = {
                                    header: "Change status ",
                                    text: "Password changed",
                                    buttons: ["OK"],
                                    buttonsAlignment: "center"
                                };     
                                dhx.alert(cError).then(function(answer){
                                    if (answer) {
                                        form.destructor();
                                        dhxWindow.destructor();
                                    }
                                });
                            }
                            if ( msg.origOK == 1 && msg.newMatch == 0) {
                                const cError = {
                                    header: "Change status ",
                                    text: "New password don't match",
                                    buttons: ["OK"],
                                    buttonsAlignment: "center"
                                };     
                                dhx.alert(cError).then(function(answer){
                                    if (answer) {
                                    }
                                });
                            }
                        });
                    };
                });         
            };
        });

        dhxWindow.attach(form);
        dhxWindow.show();
    }

    setHeader();
    mainLayout.getCell("sidebar").attach(sidebar);
    

</script>
