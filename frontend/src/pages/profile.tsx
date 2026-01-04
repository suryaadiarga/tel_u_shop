import * as React from "react"
import { Card, CardContent } from "../components/ui/card"
import { Badge } from "../components/ui/badge"
import { Input } from "../components/ui/input"
import { Button } from "../components/ui/button"
import { Alert, AlertDescription, AlertTitle } from "../components/ui/alert"
import { useAuth, useRole } from "../store/auth"
import { apiFetch, ApiError } from "../lib/api"
import { useToast } from "../components/ui/toast"
import { resolveStorageUrl } from "../lib/storage"

export function ProfilePage() {
  const { user, refresh } = useAuth()
  const role = useRole()
  const { push } = useToast()
  const [error, setError] = React.useState<string | null>(null)
  const [fieldErrors, setFieldErrors] = React.useState<Record<string, string[]>>({})
  const [busy, setBusy] = React.useState(false)
  const [passwordBusy, setPasswordBusy] = React.useState(false)
  const [passwordError, setPasswordError] = React.useState<string | null>(null)
  const [passwordFieldErrors, setPasswordFieldErrors] = React.useState<Record<string, string[]>>({})
  const [form, setForm] = React.useState({
    name: user?.name ?? "",
    email: user?.email ?? "",
  })
  const [avatar, setAvatar] = React.useState<File | null>(null)
  const [passwordForm, setPasswordForm] = React.useState({
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
  })

  React.useEffect(() => {
    if (user) {
      setForm({ name: user.name, email: user.email })
    }
  }, [user])

  if (!user) {
    return null
  }

  async function handleProfileSubmit(event: React.FormEvent) {
    event.preventDefault()
    setError(null)
    setFieldErrors({})
    setBusy(true)

    try {
      if (!user) return
      const payload = new FormData()
      if (form.name && form.name !== user.name) payload.append("name", form.name)
      if (form.email && form.email !== user.email) payload.append("email", form.email)
      if (avatar) payload.append("avatar", avatar)

      await apiFetch("/profile", {
        method: "PUT",
        body: payload,
      })
      push({ title: "Profile updated", variant: "success" })
      setAvatar(null)
      await refresh()
    } catch (err) {
      if (err instanceof ApiError) {
        setError(err.message)
        if (err.errors && typeof err.errors === "object" && !Array.isArray(err.errors)) {
          setFieldErrors(err.errors as Record<string, string[]>)
        }
      } else {
        setError("Failed to update profile")
      }
    } finally {
      setBusy(false)
    }
  }

  async function handlePasswordSubmit(event: React.FormEvent) {
    event.preventDefault()
    setPasswordError(null)
    setPasswordFieldErrors({})
    setPasswordBusy(true)

    try {
      await apiFetch("/change-password", {
        method: "PUT",
        body: passwordForm,
      })
      push({ title: "Password updated", variant: "success" })
      setPasswordForm({ current_password: "", new_password: "", new_password_confirmation: "" })
    } catch (err) {
      if (err instanceof ApiError) {
        setPasswordError(err.message)
        if (err.errors && typeof err.errors === "object" && !Array.isArray(err.errors)) {
          setPasswordFieldErrors(err.errors as Record<string, string[]>)
        }
      } else {
        setPasswordError("Failed to update password")
      }
    } finally {
      setPasswordBusy(false)
    }
  }

  const avatarUrl = resolveStorageUrl(user.avatar_url)

  return (
    <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
      <Card>
        <CardContent className="space-y-6">
          <div className="flex flex-wrap items-center justify-between gap-4">
            <div>
              <div className="font-display text-2xl font-semibold text-ink">{user.name}</div>
              <div className="text-sm text-slate-500">{user.email}</div>
            </div>
            {role ? <Badge variant="soft">{role}</Badge> : null}
          </div>

          {avatarUrl ? (
            <img
              src={avatarUrl}
              alt="Avatar"
              className="h-20 w-20 rounded-full border border-slate-200 object-cover"
            />
          ) : null}

          {error ? (
            <Alert>
              <AlertTitle>Profile update failed</AlertTitle>
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          ) : null}

          <form className="space-y-4" onSubmit={handleProfileSubmit}>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Name</label>
              <Input value={form.name} onChange={(event) => setForm((prev) => ({ ...prev, name: event.target.value }))} />
              {fieldErrors.name ? (
                <p className="text-xs text-rose-500">{fieldErrors.name.join(", ")}</p>
              ) : null}
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Email</label>
              <Input
                type="email"
                value={form.email}
                onChange={(event) => setForm((prev) => ({ ...prev, email: event.target.value }))}
              />
              {fieldErrors.email ? (
                <p className="text-xs text-rose-500">{fieldErrors.email.join(", ")}</p>
              ) : null}
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Avatar</label>
              <Input type="file" accept="image/*" onChange={(event) => setAvatar(event.target.files?.[0] ?? null)} />
              {fieldErrors.avatar ? (
                <p className="text-xs text-rose-500">{fieldErrors.avatar.join(", ")}</p>
              ) : null}
            </div>
            <Button type="submit" disabled={busy}>
              {busy ? "Saving..." : "Save changes"}
            </Button>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="space-y-6">
          <div>
            <div className="font-display text-xl font-semibold text-ink">Security</div>
            <p className="text-sm text-slate-500">Update your password.</p>
          </div>

          {passwordError ? (
            <Alert>
              <AlertTitle>Password update failed</AlertTitle>
              <AlertDescription>{passwordError}</AlertDescription>
            </Alert>
          ) : null}

          <form className="space-y-4" onSubmit={handlePasswordSubmit}>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Current password</label>
              <Input
                type="password"
                value={passwordForm.current_password}
                onChange={(event) =>
                  setPasswordForm((prev) => ({ ...prev, current_password: event.target.value }))
                }
              />
              {passwordFieldErrors.current_password ? (
                <p className="text-xs text-rose-500">{passwordFieldErrors.current_password.join(", ")}</p>
              ) : null}
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">New password</label>
              <Input
                type="password"
                value={passwordForm.new_password}
                onChange={(event) => setPasswordForm((prev) => ({ ...prev, new_password: event.target.value }))}
              />
              {passwordFieldErrors.new_password ? (
                <p className="text-xs text-rose-500">{passwordFieldErrors.new_password.join(", ")}</p>
              ) : null}
            </div>
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                Confirm new password
              </label>
              <Input
                type="password"
                value={passwordForm.new_password_confirmation}
                onChange={(event) =>
                  setPasswordForm((prev) => ({ ...prev, new_password_confirmation: event.target.value }))
                }
              />
              {passwordFieldErrors.new_password_confirmation ? (
                <p className="text-xs text-rose-500">{passwordFieldErrors.new_password_confirmation.join(", ")}</p>
              ) : null}
            </div>
            <Button type="submit" variant="secondary" disabled={passwordBusy}>
              {passwordBusy ? "Updating..." : "Update password"}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  )
}
