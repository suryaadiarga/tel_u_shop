import {
  Bell,
  Compass,
  CreditCard,
  FileText,
  LayoutDashboard,
  Package,
  ShoppingBag,
  ShoppingCart,
  Shield,
  Store,
  UserCircle,
  Wallet,
} from "lucide-react"

export type NavItem = {
  label: string
  to: string
  description: string
  icon: typeof LayoutDashboard
  roles: Array<"customer" | "merchant" | "admin">
}

export type NavSection = {
  title: string
  roles: Array<"customer" | "merchant" | "admin">
  items: NavItem[]
}

export const navSections: NavSection[] = [
  {
    title: "Core",
    roles: ["customer", "merchant", "admin"],
    items: [
      {
        label: "Overview",
        to: "/app",
        description: "Quick snapshot and shortcuts",
        icon: LayoutDashboard,
        roles: ["customer", "merchant", "admin"],
      },
      {
        label: "Profile",
        to: "/app/profile",
        description: "Manage account and security",
        icon: UserCircle,
        roles: ["customer", "merchant", "admin"],
      },
    ],
  },
  {
    title: "Customer",
    roles: ["customer"],
    items: [
      {
        label: "Catalog",
        to: "/app/catalog",
        description: "Products and categories",
        icon: ShoppingBag,
        roles: ["customer"],
      },
      {
        label: "Cart",
        to: "/app/cart",
        description: "Review your basket",
        icon: ShoppingCart,
        roles: ["customer"],
      },
      {
        label: "Orders",
        to: "/app/orders",
        description: "Track and view history",
        icon: FileText,
        roles: ["customer"],
      },
      {
        label: "Wallet",
        to: "/app/wallet",
        description: "Balance and topups",
        icon: Wallet,
        roles: ["customer"],
      },
      {
        label: "Wishlist",
        to: "/app/wishlist",
        description: "Saved favorites",
        icon: Compass,
        roles: ["customer"],
      },
      {
        label: "Loyalty",
        to: "/app/loyalty",
        description: "Points and rewards",
        icon: CreditCard,
        roles: ["customer"],
      },
      {
        label: "Notifications",
        to: "/app/notifications",
        description: "Updates and alerts",
        icon: Bell,
        roles: ["customer"],
      },
    ],
  },
  {
    title: "Merchant",
    roles: ["merchant"],
    items: [
      {
        label: "Products",
        to: "/app/merchant/products",
        description: "Catalog management",
        icon: Store,
        roles: ["merchant"],
      },
      {
        label: "Orders",
        to: "/app/merchant/orders",
        description: "Fulfillment pipeline",
        icon: Package,
        roles: ["merchant"],
      },
      {
        label: "Analytics",
        to: "/app/merchant/analytics",
        description: "Sales performance",
        icon: LayoutDashboard,
        roles: ["merchant"],
      },
    ],
  },
  {
    title: "Admin",
    roles: ["admin"],
    items: [
      {
        label: "Orders",
        to: "/app/admin/orders",
        description: "Monitor all transactions",
        icon: Package,
        roles: ["admin"],
      },
      {
        label: "Users",
        to: "/app/admin/users",
        description: "Roles and approvals",
        icon: Shield,
        roles: ["admin"],
      },
    ],
  },
]
