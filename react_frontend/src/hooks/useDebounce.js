import { useRef } from 'react';

export default function useDebounce(fn, ms = 500) {
  const tRef = useRef(null);
  return (...args) => {
    clearTimeout(tRef.current);
    tRef.current = setTimeout(() => {
      fn(...args);
    }, ms);
  };
}
