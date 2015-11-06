package application;
import java.io.*;
import java.net.*;
import java.util.*;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class Client {
	 protected static final int PORT = 3041;
	    private String serveur = "192.168.1.10";
	    private BufferedReader in = null;
	    private Socket s = null;
	    
	    // fonction de connection au serveur
	    public Client(String serveur) {
	        this.serveur = serveur;
	        boolean var = true ;
	        while (var){
	        	//System.out.println("En attente de connexion");
		        try {
		            // cr�ation de l'objet socket
		            this.s = new Socket (serveur, PORT);
		            // cr�ation du BufferedReader pour r�cup�rer les messages envoy�s sur le socket
		            this.in = new BufferedReader(new InputStreamReader(this.s.getInputStream()));
		            System.out.println("Connexion �tablie avec " + this.s.getInetAddress() + " sur le port " + this.s.getPort());
		            var = false;
		        } catch (IOException e) {
		            System.out.println("�chec de la connexion");
		        }
	        }
	    }
	    // fonction de r�ception du message sur le socket
	    public String lance() {
	        String mess = null;
	        try{
	            // boucle tant qu'il n'y a pas un message re�u
	            while (true) {
	                // r�cup�ration du message (JSON)
	                mess = this.in.readLine();
	                // si le message = null on ferme la connection
	                if (mess == null) {
	                    System.out.println("Connexion termin�e");
	                    in.close();
	                    s.close();
	                    break;
	                }
	                // on print le message re�u
	                System.out.println(mess);
	                in.close();
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
	        valueXConverted = valueX * 0.41;
	        // retourne la valeur des X en mm
	        return valueXConverted;
	    }
	    // fonction de convertion pixel/mm pour la valeur des Y
	    public static double ConvertionY(double valueY){
	        double valueYConverted = 0.0;
	        // pour une feuille de 25x18 (cm)
	        valueYConverted = valueY * 0.45;
	        // retourne la valeur des Y en mm
	        return valueYConverted;
	    }
	    // fonction de traitement de la trame JSON r�ceptionn�e
	    public static Vector2[] traitement (String chaine) {
	        int nbPoint = 0;
	        // cr�ation des objets JSONObject et JSONParser
	        JSONParser p = new JSONParser();
	        JSONObject o = null;
	        
	        Vector2[] tableau;
	        
	        try {
	            // traduction en message re�u en objet JSON
	            o = (JSONObject) p.parse(chaine);
	        } catch (ParseException e) {
	            // TODO Auto-generated catch block
	            e.printStackTrace();
	            System.out.println(e.getMessage());
	        }
	        
	        // r�cup�ration du nombre de point contenu dans la trame JSON
	        int NbPoint = Integer.parseInt(o.get("nbPoints").toString());
	        System.out.println("Nombre de points : " + NbPoint);
	        
	        tableau = new Vector2[NbPoint];
	        
	        // cr�ation d'une liste d'objet JSON contenant les param�tres de la trame JSON ("dessin")
	        ArrayList<JSONObject> liste = (ArrayList<JSONObject>) o.get("dessin");
	        // pour tous les objets de la trame ("L1","L2" etc...)
	        for(JSONObject j: liste){
	            // on "isole" le contenu des objets dans une collection
	            Collection keys = j.values();
	            // pour tous les objets de la collection ("point1", "point2", etc...)
	            for( Object obj: keys) {
	                // cr�ation d'une liste d'objets JSON contenant les objets de la collection
	                ArrayList<JSONObject> keyl = (ArrayList<JSONObject>) obj;
	                
	                // pour tous les objets de la collection ("150;100","200;50", etc...)
	                for(JSONObject pt: keyl){
	                    // cr�ation d'un it�rator pour isoler les �l�ments
	                    Iterator it = keyl.iterator();
	                    Set keys3 = pt.keySet();
	                    // pour toutes les it�rations
	                    for (Iterator i = keys3.iterator(); i.hasNext();) {
	                        // r�cup�ration du contenu en String
	                        String key = (String) i.next();
	                        String value = pt.get(key).toString();
	                        // s�paration des �l�ments � partir du symbole ";"
	                        String table[] = value.split(";",2);
	                        // on r�cup�re le premier �l�ment qui correspond � l'axe X
	                        String x = table[0];
	                        
	                        // on r�cup�re le deuxi�me �l�ment qui correspond � l'axe Y
	                        String y = table[1];
	                        // on print ces �l�ments
	                        //System.out.println("x" + nbPoint + "= " + ConvertionX(Double.parseDouble(x)));
	                        //System.out.println("y" + nbPoint + "= " + ConvertionY(Double.parseDouble(y)));
	                        
	                        tableau[nbPoint] = new Vector2(ConvertionX(Double.parseDouble(x)), ConvertionY(Double.parseDouble(y)));
	                        System.out.println(tableau[nbPoint].x);
	                        System.out.println(tableau[nbPoint].y);
	                        nbPoint++;
	                    }
	                }
	            }
	        }
	        return tableau;
	    }
	    // programme principal
	    public static void main(String [] args) {
	        String message = null;
	        // on renseigne l'adresse du serveur
	        String leServeur = "192.168.1.10";	
	        Vector2[] tableau;
	        if (args.length==1) {
	            leServeur = args[0];
	        }
	        // cr�ation du socket pour la connection au serveur
	        Client c = new Client(leServeur);
	        // lancement de la fonction pour r�cup�rer le message JSON du serveur
	        message = c.lance();
	        
	        //message = "{\"dessin\":[{\"L1\":[{\"point1\":\"146;501\"},{\"point2\":\"130;419\"}],\"L2\":[{\"point1\":\"130;501\"},{\"point2\":\"349;501\"}],\"L3\":[{\"point1\":\"349;419\"},{\"point2\":\"349;501\"}],\"L4\":[{\"point1\":\"349;419\"},{\"point2\":\"130;419\"}]}]}";
	        //System.out.println(message);
	        
	        // lancement de la fonction de traitement de la trame JSON re�u et pass�e en param�tre
	        tableau = traitement(message);
	    }
}