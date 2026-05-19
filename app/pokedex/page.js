"use client";

import { useState, useEffect } from "react";

export default function Pokedex() {
  const [name, setName] = useState("pikachu");
  const [pokemon, setPokemon] = useState(null);
  const [error, setError] = useState("");

  async function fetchPokemon() {
    try {
      setError("");

      const res = await fetch(
        `https://pokeapi.co/api/v2/pokemon/${name.toLowerCase()}`
      );

      if (!res.ok) {
        throw new Error("Pokemon not found");
      }

      const data = await res.json();
      setPokemon(data);
    } catch (err) {
      setPokemon(null);
      setError(err.message);
    }
  }

  // Load Pikachu when page opens
  useEffect(() => {
    fetchPokemon();
  }, []);

  return (
    <div style={{ textAlign: "center", padding: "20px" }}>
      <h1>Pokedex</h1>

      <input
        type="text"
        value={name}
        onChange={(e) => setName(e.target.value)}
        placeholder="Enter Pokemon name"
      />

      <button onClick={fetchPokemon}>Search</button>

      {error && <p style={{ color: "red" }}>{error}</p>}

      {pokemon && (
        <div>
          <h2>{pokemon.name.toUpperCase()}</h2>

          <img
            src={
              pokemon.sprites.other["official-artwork"].front_default ||
              pokemon.sprites.front_default
            }
            alt={pokemon.name}
            width="200"
          />

          <p>Height: {pokemon.height}</p>
          <p>Weight: {pokemon.weight}</p>
        </div>
      )}
    </div>
  );
}

/**@type {import('next').NextConfig} */
const nextConfig = {
  allowedDevOrigins: ["192.168.8.35"],
};