import {createRouter, createWebHistory} from "vue-router";
import Home from "../component/Home.vue";
import ChooseLogin from "../component/Login/ChooseLogin.vue";
import Login from "../component/Login/Login.vue";
import Redirect from "../component/Login/Redirect.vue";
import ErrorPage from "../component/Login/ErrorPage.vue";
import AccessToken from "../component/Login/AccessToken.vue";
import LabelsIndex from "../component/GitLab/Labels/LabelsIndex.vue";
import BoardIndex from "../component/GitLab/Board/BoardIndex.vue";
import ProjectIndex from "../component/GitLab/Project/ProjectIndex.vue";
import DialogCopy from "../component/GitLab/Project/Action/DialogCopy.vue";
import DialogLabels from "../component/GitLab/Project/Action/DialogLabels.vue";
import FailedJobs from "../component/Jobs/FailedJobs.vue";
import DialogException from "../component/Jobs/Action/DialogException.vue";
import DialogInfo from "../component/GitLab/Project/Action/DialogInfo.vue";
import CreateIssue from "../component/GitLab/Project/Action/CreateIssue.vue";

export const router = createRouter({
    history: createWebHistory(),
    routes:[
        {
            path:'/choose',
            name: 'Choose',
            meta: {requiresNoAuth: true},
            component: ChooseLogin,
            children:[]
        },
        {
            path:'/login',
            name: 'Login',
            meta: {requiresNoAuth: true},
            component: Login,
            children:[]
        },
        {
            path:'/redirect',
            name: 'Redirect',
            component: Redirect,
            meta: {requiresNoAuth: true},
            children:[]
        },
        {
            path:'/error',
            name: 'Error',
            component: ErrorPage,
            meta: {requiresNoAuth: true},
            children:[]
        },
        {
            path:'/',
            name: 'Home',
            component: Home,
            meta: {requiresAuth: true},
            children:[
                {
                    path:'/gitlab',
                    component:AccessToken,
                    name:'AccessToken'
                },
                {
                    path:'/gitlab/labels',
                    component:LabelsIndex,
                    name:'LabelsIndex'
                },
                {
                    path:'/gitlab/boards',
                    component:BoardIndex,
                    name:'BoardIndex'
                },
                {
                    path:'/gitlab/projects',
                    component:ProjectIndex,
                    name:'ProjectIndex',
                    children:[
                        {
                            path:'/action/:project',
                            component:DialogCopy,
                            name:'DialogCopy',
                        },
                        {
                            path:'/issue/:project',
                            component:CreateIssue,
                            name:'CreateIssue',
                        },
                        {
                            path:'/info/:id',
                            component:DialogInfo,
                            name:'DialogInfo',
                        },
                        {
                            path:'/action/labels/:title/:project',
                            component:DialogLabels,
                            name:'DialogLabels',
                        }
                    ]
                },
                {
                    path:'/failed',
                    component:FailedJobs,
                    name:'FailedJobs',
                    children:[
                        {
                            path:'/exception/:id',
                            component:DialogException,
                            name:'DialogException',
                        }
                    ]
                },

            ]
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/choose',
        }
    ]
})


