import { Card, CardContent } from "../components/ui/card"

export function MerchantPendingPage() {
  return (
    <div className="mx-auto max-w-xl">
      <Card>
        <CardContent className="space-y-3">
          <div className="font-display text-xl font-semibold text-ink">Merchant approval pending</div>
          <p className="text-sm text-slate-600">
            Your merchant account is waiting for admin approval. You can browse your profile, but merchant tools
            remain locked until approval is granted.
          </p>
        </CardContent>
      </Card>
    </div>
  )
}
