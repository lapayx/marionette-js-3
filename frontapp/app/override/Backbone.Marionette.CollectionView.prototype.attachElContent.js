import Marionette from 'backbone.marionette';

Marionette.CollectionView.prototype.attachElContent = function (html) {

    //this.$el.html(html);

    if (this.tagName.toLowerCase() == Backbone.Marionette.View.prototype.tagName.toLowerCase()) {
        let child = $(html);
        if(this.el.tagName.toLowerCase() == Backbone.Marionette.View.prototype.tagName.toLowerCase()){
          this.setElement(child);
          if (this.className) {
                  this.el.className = this.className  + " "  + this.el.className;
          }
        } else{
          this.$el.html(child.html());
        }

    } else {
      this.$el.html(html);
    }


    return this;
}
