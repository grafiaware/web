/*
 * https://replit.com/talk/share/Super-Simple-Vanilla-JS-Router-15-lines/20492
 */

export class Router {
  constructor(root) {
    this.root = root;
    this.routes = {};
  }
  get(route, callback) {
    this.routes[this.root + route] = callback;
  }
  start() {
    let currentPage = window.location.href;
    if (currentPage in this.routes) {
      this.routes[currentPage]();
    }
  }
}

//=== router ============

import { Router } from './router.js';
var router = new Router('https://yourpagehere'); // Don't include the / at the end.