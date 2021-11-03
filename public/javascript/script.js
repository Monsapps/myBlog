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

    if(document.getElementById(`social_id_${id}`)) {
        socialId = document.getElementById(`social_id_${id}`).value;
    }

    if(document.getElementById(`social_name_${id}`)) {
        socialName = document.getElementById(`social_name_${id}`).value;
    }

    if(document.getElementById(`social_image_${id}`)) {
        socialImage = document.getElementById(`social_image_${id}`).value;
    }
    
    if(document.getElementById(`social_meta_${id}`)) {
        socialMeta = document.getElementById(`social_meta_${id}`).value;
    }

    // save input with index==id
    saveInputArray.forEach((input) => {
        if(input.index === id) {
            input.index = id;
            input.socialId = socialId;
            input.socialName = socialName;
            input.socialImage = socialImage;
            input.socialMeta = socialMeta;
            edited = true;
        }
    });

    // if not edited create one
    if(!edited) {
        saveInputArray.push({ 
                    "index": id,
                    "id": socialId,
                    "socialName" : socialName,
                    "socialImage": socialImage,
                    "socialMeta": socialMeta
                    });     
    }
}

function restoreInput() {
    saveInputArray.forEach((input) => {
        if(document.getElementById(`social_id_${input.index}`)) {
            document.getElementById(`social_id_${input.index}`).value = input.id;
        }
        if(document.getElementById(`social_name_${input.index}`)) {
            document.getElementById(`social_name_${input.index}`).value = input.socialName;
        }
        if(document.getElementById(`social_image_${input.index}`)) {
            document.getElementById(`social_image_${input.index}`).value = input.socialImage;
        }
        if(document.getElementById(`social_meta_${input.index}`)) {
            document.getElementById(`social_meta_${input.index}`).value = input.socialMeta;
        }
    });
}

function deleteSocial(id) {
    document.getElementById(`social-${id}`).outerHTML="";
}
