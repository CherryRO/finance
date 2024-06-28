export default [
  { heading: 'Company and users' },
  {
    title: 'User',
    icon: { icon: 'ri-user-line' },
    children: [
      {
        title: 'List',
        to: 'apps-user-list',
      },
      {
        title: 'View',
        to: {
          name: 'apps-user-view-id',
          params: { id: 21 },
        },
      },
    ],
  },
  {
    title: 'Roles & Permissions',
    icon: { icon: 'ri-lock-2-line' },
    children: [
      {
        title: 'Roles',
        to: 'apps-roles',
        rules: { action: 'read', subject: 'Refills' },
      },
    ],
  },
]
