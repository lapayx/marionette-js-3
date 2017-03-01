import Marionette from 'backbone.marionette';
import _ from "underscore";

Marionette.View.prototype.serializeModel = function (html) {

    if (!this.model) {
        return {};
    }
    let model = _.clone(this.model.attributes);

    return _.extend(model, _.clone(this.state));

}
