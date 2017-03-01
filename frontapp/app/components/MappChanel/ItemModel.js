import Bb from "backbone";

export default Bb.Model.extend({
    urlRoot :"mapping.php",
    //url :"mapping.php",
    idAttribute:"id",
    defaults:{
        id:0,
        source:"",
        medium:"",
        channel:"",
        direction:""
    }


})
