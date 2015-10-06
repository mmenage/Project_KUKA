
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
    
    public Client(String serveur) {
        this.serveur = serveur;
        try {
            this.s = new Socket (serveur, PORT);
            this.in = new BufferedReader(new InputStreamReader(this.s.getInputStream()));
            System.out.println("Connexion établie avec " + this.s.getInetAddress() + " sur le port " + this.s.getPort());
        } catch (IOException e) {
            System.out.println("échec de la connexion");
        }
    }
    
    public String lance() {
        String mess = null;
        try{
            while (true) {
                mess = this.in.readLine();
                
                if (mess == null) {
                    System.out.println("Connexion terminée");
                    break;
                }
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
    
    public static double ConvertionX(double valueX){
        double valueXConverted = 0.0;
        // pour une feuille de 25x18 (cm)
        valueXConverted = valueX / 2.5;
        
        return valueXConverted;
    }
    public static double ConvertionY(double valueY){
        double valueYConverted = 0.0;
        // pour une feuille de 25x18 (cm)
        valueYConverted = valueY / 1.8;
        
        return valueYConverted;
    }
    
    public static void traitement(String chaine) {
        int nbPoint = 1;
        JSONParser p = new JSONParser();
        JSONObject o = null;
        try {
            o = (JSONObject) p.parse(chaine);
        } catch (ParseException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
            System.out.println(e.getMessage());
        }
        
        ArrayList<JSONObject> liste = (ArrayList<JSONObject>) o.get("dessin");
        for(JSONObject j: liste){
            Collection keys = j.values();
            for( Object obj: keys) {
                ArrayList<JSONObject> keyl = (ArrayList<JSONObject>) obj;
                nbPoint = 1;
                for(JSONObject pt: keyl){
                    Iterator it = keyl.iterator();
                    Set keys3 = pt.keySet();
                    for (Iterator i = keys3.iterator(); i.hasNext();) {
                        String key = (String) i.next();
                        String value = pt.get(key).toString();
                        String table[] = value.split(";",2);
                        String x = table[0];
                        String y = table[1];
                        System.out.println("x" + nbPoint + "= " + x);
                        System.out.println("y" + nbPoint + "= " + y);
                        nbPoint++;
                    }
                }
            }
        }
    }
    
    public static void main(String [] args) {
        String message = null;
        String leServeur = "172.30.1.117";	
        if (args.length==1) {
            leServeur = args[0];
        }
        //Client c = new Client(leServeur);
        //message = c.lance();
        
        message = "{\"dessin\":[{\"L1\":[{\"point1\":\"146;501\"},{\"point2\":\"130;419\"}],\"L2\":[{\"point1\":\"130;501\"},{\"point2\":\"349;501\"}],\"L3\":[{\"point1\":\"349;419\"},{\"point2\":\"349;501\"}],\"L4\":[{\"point1\":\"349;419\"},{\"point2\":\"130;419\"}]}]}";
        System.out.println(message);
        traitement(message);
    }
}