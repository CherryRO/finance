export default [
  { heading: 'Company and users' },
  {
    title: 'User',
    action: 'read',
    subject: 'Users',
    icon: { icon: 'ri-user-line' },
    children: [
      {
        title: 'List',
        to: 'er-list',
        action: 'read',
        subject: 'Users',
      },
      {
        title: 'View',
        to: {
          name: 'er-view-id',
          params: { id: 21 },
          action: 'read',
          subject: 'Users',
        },
      },
    ],
  },
  {
    title: 'Roles & Permissions',
    icon: { icon: 'ri-lock-2-line' },
    children: [
      { title: 'Roles', to: 'apps-roles', action: 'read', subject: 'Roles' },
    ],
  },

]
