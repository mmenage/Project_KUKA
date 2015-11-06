package application;
import java.io.*;
import java.net.*;
import java.util.*;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class Client_ModifV2 {
	protected static final int PORT = 3502;
	private String serveur = "192.168.1.10";
	private BufferedReader in = null;
	private	BufferedReader console = null;
	private PrintStream out = null;
	private Socket s = null;
	


	public Client_ModifV2(String serveur) {
		this.serveur = serveur;
		try {
			this.s = new Socket (serveur, PORT);

			this.in = new BufferedReader(new InputStreamReader(this.s.getInputStream()));
			this.console = new BufferedReader(new InputStreamReader(System.in));
			this.out = new PrintStream(this.s.getOutputStream());
			System.out.println("Connexion établie avec " + this.s.getInetAddress() + " sur le port " + this.s.getPort());
		} catch (IOException e) {
			System.out.println("échec de la connexion");
		}
	}

	public String lance() {
		String mess = null;
		try{
			while (true) {
//				mess = this.console.readLine();
//				this.out.println(mess);
				mess = this.in.readLine();
				if (mess == null) {
					System.out.println("Connexion terminée");
					s.close();
					break;
				}
				System.out.println(mess);
				s.close();
				break;
			}
		}
		catch (IOException e) {
			System.err.println(e);
			System.out.println("Error");
		}
		
		return mess;
	} 
	//
	public static double ConvertionX(double valueX){
		double valueXConverted = 0.0;
		// pour une feuille de 25x18 (cm)
		// valueXConverted = valueX / 2.5;
		valueXConverted = valueX / 1.8;
		
		return valueXConverted;
	}
	public static double ConvertionY(double valueY){
		double valueYConverted = 0.0;
		// pour une feuille de 25x18 (cm)
		//valueYConverted = valueY / 1.8;
		valueYConverted = valueY / 2.5;
		
		return valueYConverted;
	}
/*	
	public static Vector2[] traitement(String chaine) {
		
		Vector2[] tableau;
		
		
		
		double x = 0.0;
		double y = 0.0;
		JSONParser p = new JSONParser();
		JSONObject o = null;
		int nbPoint = 0;
	    try {
	    	o = (JSONObject) p.parse(chaine);
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			System.out.println(e.getMessage());
			
		}
	    
	    ArrayList<JSONObject> liste = (ArrayList<JSONObject>) o.get("dessin");
	    
	    tableau = new Vector2[liste.size()];
	    
	   	for(JSONObject j: liste) {
	   		
	   		x = Double.parseDouble(j.get("x").toString());
	   		y = Double.parseDouble(j.get("y").toString());
	   		
	   		tableau[nbPoint] = new Vector2(ConvertionX(x), ConvertionY(y));
	   		
	   		System.out.println("x" + nbPoint + ":" + ConvertionX(x));
	   		System.out.println("y" + nbPoint + ":" + ConvertionY(y));
	   		nbPoint++;
	   	}
	   	
	   	return tableau;
	   	
	   	
	}
*/	
	/*
	public static Vector2[] traitement(String chaine) {
		int nbPoint = 0;
		JSONParser p = new JSONParser();
		JSONObject o = null;
		
		Vector2[] tableau;
		//String[] table;
		
	    try {
	    	o = (JSONObject) p.parse(chaine);
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			System.out.println(e.getMessage());
		}
	   
	    ArrayList<JSONObject> liste = (ArrayList<JSONObject>) o.get("dessin");
	    
	    tableau = new Vector2[50];
	    
	    for(JSONObject j: liste){
		    Collection keys = j.values();
		    for( Object obj: keys) {
		        ArrayList<JSONObject> keyl = (ArrayList<JSONObject>) obj;
		        //nbPoint = 0;
		        for(JSONObject pt: keyl){
		        	Iterator it = keyl.iterator();
		        	Set keys3 = pt.keySet();
			        for (Iterator i = keys3.iterator(); i.hasNext();) {
			            String key = (String) i.next();
			            String value = pt.get(key).toString();
			            System.out.println(value);
			            String table[] = value.split(";",2);
			            String x = table[0];
			            String y = table[1];
			            System.out.println(x);
			            System.out.println(y);
			            
				   		tableau[nbPoint] = new Vector2(ConvertionX(Double.parseDouble(x)), ConvertionY(Double.parseDouble(y)));

			            System.out.println("x" + nbPoint + "= " + x);
			            System.out.println("y" + nbPoint + "= " + y);
			            nbPoint++;
			        }
		        }
		    }
	    }
	    return tableau;
	}
*/
	// fonction de traitement de la trame JSON réceptionnée
		public static Vector2[] traitement (String chaine) {
			int nbPoint = 0;
			// création des objets JSONObject et JSONParser
			JSONParser p = new JSONParser();
			JSONObject o = null;
			
			Vector2[] tableau;
			
		    try {
		    	// traduction en message reçu en objet JSON
		    	o = (JSONObject) p.parse(chaine);
			} catch (ParseException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				System.out.println(e.getMessage());
			}
		    
		    // récupération du nombre de point contenu dans la trame JSON
		    int NbPoint = Integer.parseInt(o.get("nbPoints").toString());
		   	System.out.println("Nombre de points : " + NbPoint);
		   	
		   	tableau = new Vector2[NbPoint];
		   	
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
			        //nbPoint = 0;
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
	
	public static void main(String [] args) {
		String message = null;
		String leServeur = "192.168.1.10";	
		if (args.length==1) {
			leServeur = args[0];
		}
		
		Client_ModifV2 c = new Client_ModifV2(leServeur);
		
		message = c.lance();
		/*JSONObject p1 = new JSONObject();
		p1.put("x", 0);
		p1.put("y", 0);
		
		JSONObject p2 = new JSONObject();
		p2.put("x", 1);
		p2.put("y", 0);
		
		JSONObject p3 = new JSONObject();
		p3.put("x", 0);
		p3.put("y", 1);
		
		ArrayList<JSONObject> liste = new ArrayList<JSONObject>();
	    liste.add(p1);
	    liste.add(p2);
	    liste.add(p3);
	    
		JSONObject json = new JSONObject();
		json.put("dessin",liste);
		System.err.println(json.toJSONString());*/
		
		//traitement(json.toJSONString());
		traitement(message);
	}
}