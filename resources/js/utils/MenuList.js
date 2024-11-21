import FailedJobs from "../component/Jobs/FailedJobs.vue";

export const MenuList = (isRoot) => {

    // if(isRoot){
    return [
        {
            text:'Labels',
            icon: 'mdi-label',
            routeName: 'LabelsIndex',
            children:[]
        },
        {
            text:'Boards',
            icon: 'mdi-view-dashboard',
            routeName: 'BoardIndex',
            children:[]
        },
        {
            text:'Projects',
            icon: 'mdi-database-check',
            routeName: 'ProjectIndex',
            children:[]
        },
        {
            text:'Failed Jobs',
            icon: 'mdi-message-alert',
            routeName: 'FailedJobs',
            children:[]
        },

    ]

}
