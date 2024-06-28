import companyAndUsers from './company-and-users'
import dashboard from './dashboard'
import type { VerticalNavItems } from '@layouts/types'

export default [...dashboard, ...companyAndUsers,] as VerticalNavItems
