
import java.io.*;
import java.net.*;
import java.util.*;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class Client {
    protected static final int PORT = 3344;
    private String serveur = "172.30.1.117";
    private BufferedReader in = null;
    private Socket s = null;
    // fonction de connection au serveur
    public Client(String serveur) {
        this.serveur = serveur;
        try {
            // création de l'objet socket
            this.s = new Socket (serveur, PORT);
            // création du BufferedReader pour récupérer les messages envoyés sur le socket
            this.in = new BufferedReader(new InputStreamReader(this.s.getInputStream()));
            System.out.println("Connexion établie avec " + this.s.getInetAddress() + " sur le port " + this.s.getPort());
        } catch (IOException e) {
            System.out.println("échec de la connexion");
        }
    }
    // fonction de réception du message sur le socket
    public String lance() {
        String mess = null;
        try{
            // boucle tant qu'il n'y a pas un message reçu
            while (true) {
                // récupération du message (JSON)
                mess = this.in.readLine();
                // si le message = null on ferme la connection
                if (mess == null) {
                    System.out.println("Connexion terminée");
                    break;
                }
                // on print le message reçu
                System.out.println(mess);
                break;
            }
        }
        catch (IOException e) {
            System.err.println(e);
            System.out.println("Error");
        }
        return mess;
    }
    // fonction de convertion pixel/mm pour la valeur des X
    public static double ConvertionX(double valueX){
        double valueXConverted = 0.0;
        // pour une feuille de 25x18 (cm)
        valueXConverted = valueX / 2.5;
        // retourne la valeur des X en mm
        return valueXConverted;
    }
    // fonction de convertion pixel/mm pour la valeur des Y
    public static double ConvertionY(double valueY){
        double valueYConverted = 0.0;
        // pour une feuille de 25x18 (cm)
        valueYConverted = valueY / 1.8;
        // retourne la valeur des Y en mm
        return valueYConverted;
    }
    // fonction de traitement de la trame JSON réceptionnée
    public static void traitement(String chaine) {
        int nbPoint = 1;
        // création des objets JSONObject et JSONParser
        JSONParser p = new JSONParser();
        JSONObject o = null;
        try {
            // traduction en message reçu en objet JSON
            o = (JSONObject) p.parse(chaine);
        } catch (ParseException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
            System.out.println(e.getMessage());
        }
        // création d'une liste d'objet JSON contenant les paramètres de la trame JSON ("dessin")
        ArrayList<JSONObject> liste = (ArrayList<JSONObject>) o.get("dessin");
        // pour tous les objets de la trame ("L1","L2" etc...)
        for(JSONObject j: liste){
            // on "isole" le contenu des objets dans une collection
            Collection keys = j.values();
            // pour tous les objets de la collection ("point1", "point2", etc...)
            for( Object obj: keys) {
                // création d'une liste d'objets JSON contenant les objets de la collection
                ArrayList<JSONObject> keyl = (ArrayList<JSONObject>) obj;
                nbPoint = 1;
                // pour tous les objets de la collection ("150;100","200;50", etc...)
                for(JSONObject pt: keyl){
                    // création d'un itérator pour isoler les éléments
                    Iterator it = keyl.iterator();
                    Set keys3 = pt.keySet();
                    // pour toutes les itérations
                    for (Iterator i = keys3.iterator(); i.hasNext();) {
                        // récupération du contenu en String
                        String key = (String) i.next();
                        String value = pt.get(key).toString();
                        // séparation des éléments à partir du symbole ";"
                        String table[] = value.split(";",2);
                        // on récupére le premier élément qui correspond à l'axe X
                        String x = table[0];
                        // on récupére le deuxième élément qui correspond à l'axe Y
                        String y = table[1];
                        // on print ces éléments
                        System.out.println("x" + nbPoint + "= " + x);
                        System.out.println("y" + nbPoint + "= " + y);
                        nbPoint++;
                    }
                }
            }
        }
    }
    // programme principal
    public static void main(String [] args) {
        String message = null;
        // on renseigne l'adresse du serveur
        String leServeur = "172.30.1.117";
        if (args.length==1) {
            leServeur = args[0];
        }
        // création du socket pour la connection au serveur
        Client c = new Client(leServeur);
        // lancement de la fonction pour récupérer le message JSON du serveur
        message = c.lance();
        
        //message = "{\"dessin\":[{\"L1\":[{\"point1\":\"146;501\"},{\"point2\":\"130;419\"}],\"L2\":[{\"point1\":\"130;501\"},{\"point2\":\"349;501\"}],\"L3\":[{\"point1\":\"349;419\"},{\"point2\":\"349;501\"}],\"L4\":[{\"point1\":\"349;419\"},{\"point2\":\"130;419\"}]}]}";
        //System.out.println(message);
        
        // lancement de la fonction de traitement de la trame JSON reçu et passée en paramètre
        traitement(message);
    }
}