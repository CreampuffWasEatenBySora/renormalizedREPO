function removeNodeByAttribute(obj, attributeName, attributeValue) {
    
    if (Array.isArray(obj)) {
        
        for (let i = obj.length - 1; i < i >= 0; i--) {
            removeNodeByAttribute(obj[i], attributeName, attributeValue);
        } 

    } else if(typeof obj === 'object' && obj != null ) {
        for(let key in obj){

            if (obj.hasOwnProperty(key)) {
                
                if (typeof obj[key] === 'object' && obj[key] !== null) {
                    removeNodeByAttribute(obj[key], attributeName, attributeValue);
                } else if (key === attributeName && obj[key] === attributeValue) {
                    delete obj[key];
                    return;
                    console.log("Deleted: " + attributeName + " " + attributeValue);
                }

            }
        }
    }

}

