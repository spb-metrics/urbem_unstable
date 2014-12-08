<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Extensão da Classe de Mapeamento TTCETOCredor
    *
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCETOCredor.class.php 60902 2014-11-21 17:56:16Z arthur $
    *
    * @ignore
    *
*/
class TTCETOCredor extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOCredor()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCredor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaCredor(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaCredor().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCredor()
    {
        $stSql  = " SELECT
                            cod_und_gestora
                            ,exercicio
                            ,cod_credor
                            ,nome
                            ,insc_estadual
                            ,insc_municipal
                            ,endereco
                            ,cidade
                            ,uf
                            ,cep
                            ,fone
                            ,fax
                            ,tipo
                            ,numero_registro
                            ,data
                            ,sigla_3  AS pais
                    FROM(
                            SELECT
                                    ( SELECT PJ.cnpj
                                        FROM orcamento.entidade
                                        JOIN sw_cgm
                                          ON sw_cgm.numcgm = entidade.numcgm
                                        JOIN sw_cgm_pessoa_juridica AS PJ
                                          ON sw_cgm.numcgm = PJ.numcgm
                                        WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                          AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                                    ) AS Cod_Und_Gestora
                                   , '".$this->getDado('exercicio')."' AS exercicio                                                                                                      
                                   , SW.numcgm
                                   , CASE WHEN PF.numcgm IS NOT NULL
                                          THEN REPLACE(REPLACE(REPLACE(PF.cpf,'-',''),' ',''),'','')
                                          ELSE REPLACE(REPLACE(REPLACE(PJ.cnpj,'-',''),' ',''),'','')
                                     END AS Cod_Credor
                                   , SW.nom_cgm AS nome
                                   , PJ.insc_estadual
                                   , CASE WHEN MAX(EF.inscricao_economica) IS NOT NULL THEN
                                               MAX(replace(EF.inscricao_economica::VARCHAR,'-','')::INTEGER)            
                                          WHEN MAX(ED.inscricao_economica) IS NOT NULL THEN
                                               MAX(replace(ED.inscricao_economica::VARCHAR,'-','')::INTEGER)            
                                          WHEN MAX(EA.inscricao_economica) IS NOT NULL THEN
                                               MAX(replace(EA.inscricao_economica::VARCHAR,'-','')::INTEGER)            
                                          ELSE NULL                                                                                               
                                     END AS insc_municipal
                                   , sem_acentos(sw_tipo_logradouro.nom_tipo)||' '||sem_acentos(sw_nome_logradouro.nom_logradouro)||' nº:'||SW.numero||' '||sem_acentos(SW.complemento) AS endereco
                                   , SM.nom_municipio AS cidade
                                   , SF.sigla_uf AS UF
                                   , sw_cgm_logradouro.cep
                                   , CASE WHEN SW.fone_comercial IS NOT NULL AND SW.fone_comercial !='0' AND SW.fone_comercial !=''
                                          THEN TRIM(SW.fone_comercial)
                                          ELSE TRIM(SW.fone_residencial)
                                     END AS Fone
                                   , TRIM(SW.fone_comercial) AS fax
                                   , credor.tipo
                                   , CASE WHEN PF.numcgm IS NOT NULL
                                           THEN PF.cpf
                                           ELSE PJ.cnpj
                                     END AS numero_registro
                                   , TO_char(SW.dt_cadastro, 'dd/mm/yyyy') as data
                                   , pais.sigla_3
                             FROM  sw_cgm AS SW
                             
                            LEFT JOIN sw_cgm_pessoa_fisica AS PF                                                                               
                                   ON SW.numcgm = PF.numcgm 
                                                                                                              
                            LEFT JOIN sw_cgm_pessoa_juridica AS PJ                                                                             
                                   ON SW.numcgm = PJ.numcgm 
                                                                                                              
                            LEFT JOIN sw_cgm_logradouro                                                                                           
                                   ON sw_cgm_logradouro.numcgm = SW.numcgm                                                                        
                            
                            LEFT JOIN sw_nome_logradouro 
                                   ON sw_nome_logradouro.cod_logradouro = sw_cgm_logradouro.cod_logradouro                                        
                                  AND sw_nome_logradouro.timestamp = ( SELECT MAX(timestamp) FROM sw_nome_logradouro AS logradouro WHERE logradouro.cod_logradouro = sw_nome_logradouro.cod_logradouro ) 
                            
                            LEFT JOIN sw_tipo_logradouro  
                                   ON sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo                                                   
                            
                            LEFT JOIN sw_bairro      
                                   ON sw_bairro.cod_bairro    = sw_cgm_logradouro.cod_bairro                                                         
                                  AND sw_bairro.cod_municipio = sw_cgm_logradouro.cod_municipio                                                   
                                  AND sw_bairro.cod_uf        = sw_cgm_logradouro.cod_uf   
                                                                                        
                            LEFT JOIN economico.cadastro_economico_empresa_fato AS EF                                                             
                                   ON EF.numcgm = SW.numcgm          
                                                                                           
                            LEFT JOIN economico.cadastro_economico_empresa_direito AS ED                                                          
                                ON ED.numcgm = SW.numcgm   
                                                                                                  
                            LEFT JOIN economico.cadastro_economico_autonomo AS EA                                                                 
                                ON EA.numcgm = SW.numcgm
                            
                            LEFT JOIN tceto.credor
                                   ON credor.exercicio = '".$this->getDado('exercicio')."'
                                  AND credor.numcgm    = SW.numcgm
                                  
                            INNER JOIN sw_municipio AS SM
                                    ON SW.cod_municipio = SM.cod_municipio 
                                   AND SW.cod_uf        = SM.cod_uf
                            
                            INNER JOIN sw_uf AS SF
                                    ON SM.cod_uf = SF.cod_uf
                                    
                            INNER JOIN sw_pais AS pais
                                    ON pais.cod_pais = SF.cod_pais
                            
                            WHERE SW.cod_municipio = SM.cod_municipio 
                              AND SW.cod_uf        = SM.cod_uf
                              AND SM.cod_uf        = SF.cod_uf  
                              AND pais.cod_pais    = SF.cod_pais                                                                    
                              AND SW.numcgm in                                                                                                
                                    ( SELECT EP.cgm_beneficiario                                                                             
                                        FROM empenho.empenho AS EE
                                        
                                  INNER JOIN empenho.pre_empenho AS EP
                                          ON EE.exercicio       = EP.exercicio                                                          
                                         AND EE.cod_pre_empenho = EP.cod_pre_empenho
                                        
                                        WHERE EE.exercicio = '".$this->getDado('exercicio')."'
                                          AND EE.dt_empenho BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."', 'dd/mm/yyyy')
                                          AND EE.cod_entidade IN (".$this->getDado('cod_entidade').")                                                                               
                                    )
                            
                            GROUP BY  SW.numcgm
                                    , SW.nom_cgm
                                    , pf.numcgm                                                                                       
                                    , pj.cnpj
                                    , Cod_Credor                                                                                                     
                                    , insc_estadual
                                    , endereco                                                                                                     
                                    , sw_cgm_logradouro.cod_logradouro                                                                             
                                    , SM.nom_municipio                                                                                             
                                    , SF.sigla_uf                                                                                                  
                                    , sw_cgm_logradouro.cep
                                    , SW.fone_comercial                                                                                            
                                    , fax
                                    , credor.tipo
                                    , data
                                    , pais.sigla_3
                                    
                            ORDER BY numcgm ASC
                    ) AS tbl ";
        
        return $stSql;
    }
}
?>
