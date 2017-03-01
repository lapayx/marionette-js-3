import './styles/application.css';
import './override/Backbone.Marionette.View.prototype.tagName';
import  './override/Backbone.Marionette.View.prototype.attachElContent';
import  './override/Backbone.Marionette.CollectionView.prototype.attachElContent';
import './override/Backbone.Marionette.View.prototype.serializeModel';

import 'bootstrap/dist/js/bootstrap.min';
import App from 'components/App';

document.addEventListener('DOMContentLoaded', () => {
  const app = new App();
  app.start();
});
