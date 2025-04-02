import React, { useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Loader } from "lucide-react";

const GitHubProfileSearch = () => {
  const [username, setUsername] = useState("");
  const [userData, setUserData] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);

  const fetchGitHubUser = async () => {
    setLoading(true);
    setError(null);
    setUserData(null);

    try {
      const response = await fetch(`https://api.github.com/users/${username}`);
      if (!response.ok) {
        throw new Error("Usuário não encontrado");
      }
      const data = await response.json();
      setUserData(data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">
      <Card className="w-full max-w-md p-4 shadow-lg bg-white rounded-2xl">
        <CardContent>
          <h1 className="text-2xl font-bold text-center mb-4">Buscar Perfil no GitHub</h1>
          <div className="flex space-x-2">
            <Input
              type="text"
              placeholder="Digite o nome de usuário"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
            />
            <Button onClick={fetchGitHubUser} disabled={loading}>
              {loading ? <Loader className="animate-spin" /> : "Buscar"}
            </Button>
          </div>

          {error && <p className="text-red-500 text-center mt-4">{error}</p>}

          {userData && (
            <div className="text-center mt-6">
              <img
                src={userData.avatar_url}
                alt={userData.name}
                className="w-24 h-24 rounded-full mx-auto border shadow-lg"
              />
              <h2 className="text-xl font-semibold mt-2">{userData.name}</h2>
              <p className="text-gray-600">{userData.bio}</p>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
};

export default GitHubProfileSearch;
