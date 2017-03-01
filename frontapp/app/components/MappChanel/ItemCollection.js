import Bb from "backbone";
import model from "./ItemModel";

export default Bb.Collection.extend({
    model:model,
    url:"http://ops.marketing.rockwellautomation.com/portal/new/mapping.php"

});
