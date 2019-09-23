import VueRouter from 'vue-router';
import Home from './views/Home';
import About from './views/About';

export default new VueRouter({
    routes: [
        {
            path: '/',
            component: Home
        },
    ],
    linkActiveClass: 'is-active'
});
