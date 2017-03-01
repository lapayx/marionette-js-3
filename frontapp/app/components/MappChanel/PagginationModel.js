import Bb from "backbone";

export default Bb.Model.extend({
    urlRoot :"mapping.php/count",
    //url :"mapping.php",
    idAttribute:"id",
    defaults:{
        totalRecord: 0,
        currentPage:1,
        pageRange:25,
        filter:{}
    }


})
