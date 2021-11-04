/**
 * Javascript for myBlog
 */

 let index = 0;

 let saveInputArray = [];

 /**
  * Save and restore already inputed fields
  * @param {*} id int
  */

 function saveInput(id) {

    var edited = false;

    var socialId;
    var socialName;
    var socialImage;
    var socialMeta;

    socialId = (document.getElementById(`social_id_${id}`) ? document.getElementById(`social_id_${id}`).value : "");
    socialName = (document.getElementById(`social_name_${id}`) ? document.getElementById(`social_name_${id}`).value : "");
    socialImage = (document.getElementById(`social_image_${id}`) ? document.getElementById(`social_image_${id}`).value : "");
    socialMeta = (document.getElementById(`social_meta_${id}`) ? document.getElementById(`social_meta_${id}`).value : "");

    // save input with index==id
    saveInputArray.forEach((input) => {
        if(input.index === id) {
            input.index = id;
            input.socialId = socialId;
            input.socialNameData = socialName;
            input.socialImageData = socialImage;
            input.socialMetaData = socialMeta;
            edited = true;
        }
    });

    // if not edited create one
    if(!edited) {
        saveInputArray.push({ 
                    "index": id,
                    "id": socialId,
                    "socialNameData": socialName,
                    "socialImageData": socialImage,
                    "socialMetaData": socialMeta
                    });
    }
}

function restoreInput() {
    saveInputArray.forEach((input) => {
        if(document.getElementById(`social_id_${input.index}`)) {
            document.getElementById(`social_id_${input.index}`).value = input.id;
        }
        if(document.getElementById(`social_name_${input.index}`)) {
            document.getElementById(`social_name_${input.index}`).value = input.socialNameData;
        }
        if(document.getElementById(`social_image_${input.index}`)) {
            document.getElementById(`social_image_${input.index}`).value = input.socialImageData;
        }
        if(document.getElementById(`social_meta_${input.index}`)) {
            document.getElementById(`social_meta_${input.index}`).value = input.socialMetaData;
        }
    });
}

function deleteSocial(id) {
    document.getElementById(`social-${id}`).outerHTML="";
}
