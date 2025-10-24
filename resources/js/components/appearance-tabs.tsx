import { HTMLAttributes } from 'react';

// Appearance controls removed. Keep a lightweight placeholder to avoid breaking imports.
export default function AppearanceToggleTab({
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement>) {
    return (
        <div className={"inline-flex gap-1 rounded-lg bg-neutral-100 p-1 " + className} {...props}>
            <div className="px-3.5 py-1.5 text-sm">Light</div>
        </div>
    );
}
