import FailedJobs from "../component/Jobs/FailedJobs.vue";

export const MenuList = (isRoot) => {

    // if(isRoot){
    return [
        {
            text:'Labels Base',
            icon: 'mdi-label',
            routeName: 'LabelsIndex',
            children:[]
        },
        {
            text:'Boards Base',
            icon: 'mdi-view-dashboard',
            routeName: 'BoardIndex',
            children:[]
        },
        {
            text:'Lista progetti GitLab',
            icon: 'mdi-git',
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
