import { type ImgHTMLAttributes } from 'react';

export default function AppLogoIcon(props: ImgHTMLAttributes<HTMLImageElement>) {
    const { src, alt = 'logo', ...rest } = props as ImgHTMLAttributes<HTMLImageElement>;

    return (
        <img
            src={src || '/logotusnobg.svg'}
            alt={alt}
            {...rest}
        />
    );
}
