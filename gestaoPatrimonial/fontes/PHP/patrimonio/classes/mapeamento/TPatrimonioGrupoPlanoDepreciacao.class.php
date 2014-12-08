<?php
/**
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioGrupoPlanoDepreciacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioGrupoPlanoDepreciacao()
{
    parent::Persistente();
    $this->setTabela('patrimonio.grupo_plano_depreciacao');
    $this->setCampoCod('cod_grupo');
    $this->setComplementoChave('cod_natureza,exercicio,cod_plano');
    $this->AddCampo('cod_grupo','integer',true,'',true,false);
    $this->AddCampo('cod_natureza','integer',true,'',true,"TPatrimonioNatureza");
    $this->AddCampo('exercicio','varchar',true,'4',true,false);
    $this->AddCampo('cod_plano','integer',true,'',true,false);

}

public function recuperaPlanoDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPlanoDepreciacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
    return $obErro;
}

public function montaRecuperaPlanoDepreciacao()
{
    $stSql = "SELECT grupo_plano_depreciacao.cod_plano AS cod_plano,
                           plano_conta.nom_conta,
                           grupo_plano_depreciacao.cod_natureza,
                           grupo_plano_depreciacao.cod_grupo,
                           grupo_plano_depreciacao.exercicio
                      FROM patrimonio.grupo_plano_depreciacao
                 
                 LEFT JOIN contabilidade.plano_analitica
                        ON plano_analitica.cod_plano = grupo_plano_depreciacao.cod_plano
                       AND plano_analitica.exercicio = grupo_plano_depreciacao.exercicio
                 
                 LEFT JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio \n";
    
    return $stSql;
    
}

}
