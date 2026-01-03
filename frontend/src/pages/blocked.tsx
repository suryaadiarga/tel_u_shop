import { Button } from "../components/ui/button"
import { Card, CardContent } from "../components/ui/card"
import { useAuth } from "../store/auth"

export function BlockedPage() {
  const { logout } = useAuth()

  return (
    <div className="mx-auto max-w-xl">
      <Card>
        <CardContent className="space-y-3">
          <div className="font-display text-xl font-semibold text-ink">Account blocked</div>
          <p className="text-sm text-slate-600">
            Your account is blocked. Please contact support or an administrator to resolve this status.
          </p>
          <Button variant="outline" onClick={logout}>
            Logout
          </Button>
        </CardContent>
      </Card>
    </div>
  )
}
